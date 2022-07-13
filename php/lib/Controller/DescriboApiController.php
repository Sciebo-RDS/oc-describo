<?php

namespace OCA\Describo\Controller;

require __DIR__ . '/../../vendor/autoload.php';

use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Core\Util\RSAKey;

use \OCA\OAuth2\Db\ClientMapper;
use OCP\IUserSession;
use OCP\IURLGenerator;

use OCP\IRequest;
use OCP\AppFramework\{
    ApiController
};
use OCP\IConfig;

/**
- Define a new api controller
 */

class DescriboApiController extends ApiController
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

    use Errors;


    public function __construct(
        $AppName,
        IRequest $request,
        $userId,
        ClientMapper $clientMapper,
        IUserSession $userSession,
        IURLGenerator $urlGenerator,
        IConfig $config
    ) {
        parent::__construct($AppName, $request);
        $this->appName = $AppName;
        $this->userId = $userId;
        $this->clientMapper = $clientMapper;
        $this->userSession = $userSession;
        $this->urlGenerator = $urlGenerator;

        $this->config = $config;

        $this->jwk = RSAKey::createFromJWK(JWKFactory::createRSAKey(
            4096 // Size in bits of the key. We recommend at least 2048 bits.
        ));

        $this->private_key = $this->config->getAppValue("describo", "privatekey", "");
        $this->public_key = $this->config->getAppValue("describo", "publickey", "");

        if ($this->private_key === "") {
            $this->public_key = RSAKey::toPublic($this->jwk)->toPEM();
            $this->private_key = $this->jwk->toPEM();

            $this->config->setAppValue("describo", "privatekey", $this->private_key);
            $this->config->setAppValue("describo", "publickey", $this->public_key);
        }
    }

    /**
     * @PublicPage
     * @CORS
     *
     * Returns the public key for mailadress
     *
     * @return object an object with publickey
     */
    public function publickey()
    {
        return $this->handleNotFound(function () {
            $data = [
                "publickey" =>  $this->public_key
            ];
            return $data;
        });
    }
}
