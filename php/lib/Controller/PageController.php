<?php

namespace OCA\Describo\Controller;

use \OCA\OAuth2\Db\ClientMapper;
use OCP\IUserSession;
use OCP\IURLGenerator;
use OCP\AppFramework\Http\RedirectResponse;
use OCP\IRequest;
use OCP\AppFramework\{
    Controller,
    Http\TemplateResponse
};
use OCA\OAuth2\Db\{AccessTokenMapper, RefreshTokenMapper};
use OCA\OAuth2\Controller\{OAuthApiController};
use OCP\ILogger;
use OCP\IConfig;

require("describo/configuration.php");

/**
- Define a new page controller
 */

class PageController extends Controller
{
    protected $appName;
    private $userId;

    /**
     * @var IURLGenerator
     */
    private $urlGenerator;

    private $public_key;
    private $private_key;
    private $jwsBuilder;

    private $config;
    private $accessTokenMapper;
    private $refreshTokenMapper;
    private $clientMapper;
    private $oauthApi;
    private $logger;

    use Errors;


    public function __construct(
        $AppName,
        IRequest $request,
        $userId,
        ClientMapper $clientMapper,
        IUserSession $userSession,
        AccessTokenMapper $accessTokenMapper,
        RefreshTokenMapper $refreshTokenMapper,
        IURLGenerator $urlGenerator,
        IConfig $config,
        OAuthApiController $oauthapi,
        ILogger $logger
    ) {
        parent::__construct($AppName, $request);
        $this->appName = $AppName;
        $this->userId = $userId;
        $this->clientMapper = $clientMapper;
        $this->userSession = $userSession;
        $this->urlGenerator = $urlGenerator;
        $this->accessTokenMapper = $accessTokenMapper;
        $this->refreshTokenMapper = $refreshTokenMapper;
        $this->clientMapper = $clientMapper;
        $this->config = $config;
        $this->oauthApi = $oauthapi;
        $this->logger = $logger;
    }

    /**
     * @NoCSRFRequired
     * @NoAdminRequired
     */
    public function authorize($code, $access_token, $refresh_token, $expires_in)
    {
        if ($code !== null) {
            $client = $this->clientMapper->findByName($this->config->getAppValue($this->appName, "oauthname", "describo"));
            $_SERVER["PHP_AUTH_USER"] = $client->getIdentifier();
            $_SERVER["PHP_AUTH_PW"] = $client->getSecret();
            $genToken = $this->oauthApi->generateToken(
                "authorization_code",
                $code,
                $client->getRedirectUri()
            )->getData();
            $access_token = $genToken["access_token"];
            $refresh_token = $genToken["refresh_token"];
            $expires_in = $genToken["expires_in"];
        }
        $this->config->setUserValue($this->userId, $this->appName, "access_token", $access_token);
        $this->config->setUserValue($this->userId, $this->appName, "refresh_token", $refresh_token);
        $this->config->setUserValue($this->userId, $this->appName, "expires_on", \time() + $expires_in);

        return new RedirectResponse(
            $this->urlGenerator->linkToRouteAbsolute("describo.page.index")
        );
    }

    private function describoSession()
    {
        function my_server_url()
        {
            $server_name = $_SERVER['SERVER_NAME'];

            if (!in_array($_SERVER['SERVER_PORT'], [80, 443])) {
                $port = ":$_SERVER[SERVER_PORT]";
            } else {
                $port = '';
            }

            if (!empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on' || $_SERVER['HTTPS'] == '1')) {
                $scheme = 'https';
            } else {
                $scheme = 'http';
            }
            return $scheme . '://' . $server_name . $port;
        }

        $url = $this->config->getAppValue($this->appName, "apiURL", "http://ui:9000");
        $secret = $this->config->getAppValue($this->appName, "describoSecretKey", "describo");

        $user = \OC::$server->getUserSession()->getUser();
        $data = [
            "eMail" => $user->getEMailAddress(),
            "userName" => $user->getUserName(),
            "displayName" => $user->getDisplayName(),
            "accountId" => $user->getAccountId(),
            "UID" => $user->getUID(),
            "lastLogin" => $user->getLastLogin(),
            "home" => $user->getHome(),
            "avatarImage" => $user->getAvatarImage($user),
            "quota" => $user->getQuota(),
            "searchTerms" => $user->getSearchTerms(),
            "webdav" => my_server_url() . "/remote.php/webdav",
            "access_token" => $this->config->getUserValue($this->userId, $this->appName, "access_token", null),
            "expires_on" => $this->config->getUserValue($this->userId, $this->appName, "expires_on", -1)
        ];

        $payload = json_encode([
            "email" => $data["eMail"],
            "name" => $data["userName"],
            "session" => $data
        ]);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' .  $secret
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $json = json_decode($server_output, true);
        $sessId = $json["sessionId"];
        $this->config->setUserValue($this->userId, $this->appName, "descSessionId", $sessId);
        return $sessId;
    }

    /**
     * @NoCSRFRequired
     * @NoAdminRequired
     */
    public function index()
    {
        $iframeUrl = $this->config->getAppValue($this->appName, "uiURL", constant("\OCA\Describo\\uiURL"));
        $url = parse_url($iframeUrl);
        $policy = new \OCP\AppFramework\Http\EmptyContentSecurityPolicy();
        $http = $url["scheme"] . "://" . $url["host"] . ":" . $url["port"];
        $ws  = str_replace($url["scheme"], "http", "ws") . "://" . $url["host"] . ":" . $url["port"];
        $policy->addAllowedConnectDomain($http);
        $policy->addAllowedConnectDomain($ws);
        $policy->addAllowedConnectDomain($http);
        $policy->addAllowedConnectDomain($ws);
        $policy->addAllowedScriptDomain($http);
        $policy->addAllowedFrameDomain($http);
        \OC::$server->getContentSecurityPolicyManager()->addDefaultPolicy($policy);

        $expires_on = $this->config->getUserValue($this->userId, $this->appName, "expires_on", -1);
        if (\time() > intval($expires_on)) {
            $access_token = null;
        } else {
            $access_token = $this->config->getUserValue($this->userId, $this->appName, "access_token", null);
        }

        $redirect = false;
        if (\time() > intval($expires_on) || $access_token === null) {
            $client = $this->clientMapper->findByName($this->config->getAppValue($this->appName, "oauthname", "describo"));
            $clientId = $client->getId();
            echo $access_token;

            foreach ($this->accessTokenMapper->findAll() as $token) {
                if ($token->getClientId() == $clientId && $token->getUserId() == $this->userId) {
                    if (\time() - intval($token->getExpires()) < 300) {
                        $access_token = $token->getToken();
                    } else {
                        $_SERVER["PHP_AUTH_USER"] = $client->getIdentifier();
                        $_SERVER["PHP_AUTH_PW"] = $client->getSecret();
                        $redirect = true;
                        foreach ($this->refreshTokenMapper->findAll() as $token) {
                            if ($token->getClientId() == $clientId && $token->getUserId() == $this->userId) {
                                $genToken = $this->oauthApi->generateToken(
                                    "refresh_token",
                                    null,
                                    null,
                                    $token->getToken()
                                )->getData();
                                $access_token = $genToken["access_token"];
                                $this->config->setUserValue($this->userId, $this->appName, "access_token", $access_token);
                                $this->config->setUserValue($this->userId, $this->appName, "refresh_token", $genToken["refresh_token"]);
                                $this->config->setUserValue($this->userId, $this->appName, "expires_on", \time() + $genToken["expires_in"]);
                                $redirect = false;
                            }
                        }
                    }
                }
            }

            if ($access_token === null || $redirect) {
                return new RedirectResponse(
                    $this->urlGenerator->linkToRouteAbsolute("oauth2.page.authorize", [
                        "response_type" => "code",
                        "client_id" => $client->getIdentifier(),
                        "redirect_uri" => $client->getRedirectUri()
                    ])
                );
            }
        }

        $iframeUrl .= "?sid=" . $this->describoSession();
        return new TemplateResponse('describo', "main.research", ["iframeSource" => $iframeUrl]);
    }
}
