<?php

namespace OCA\Describo\Panels;

use \OCA\OAuth2\Db\ClientMapper;
use OCP\IURLGenerator;
use OCP\IUserSession;
use OCP\Settings\ISettings;
use OCP\Template;
use OCP\IConfig;

require("describo/configuration.php");


class AdminPanel implements ISettings
{
    private $appName;
    /**
     * @var \OCA\OAuth2\Db\ClientMapper
     */
    private $clientMapper;
    /**
     * @var IUserSession
     */
    private $userSession;

    /**
     * @var IURLGenerator
     */
    private $urlGenerator;
    private $config;


    public function __construct(
        $AppName,
        ClientMapper $clientMapper,
        IUserSession $userSession,
        IURLGenerator $urlGenerator,
        IConfig $config
    ) {
        $this->appName = $AppName;
        $this->config = $config;
        $this->clientMapper = $clientMapper;
        $this->userSession = $userSession;
        $this->urlGenerator = $urlGenerator;
    }

    public function getSectionID()
    {
        return 'describo';
    }

    /**
     * @return Template
     */
    public function getPanel()
    {
        $t = new Template($this->appName, 'settings-admin');

        $assignValue = function ($field) use ($t) {
            $const = constant("\OCA\Describo\\" . $field);
            $val = $this->config->getAppValue($this->appName, $field);

            if ($val == null || empty($val)) {
                $this->config->setAppValue($this->appName, $field, $const);
                $val = $const;
            }
            
            $t->assign($field, $val);
        };

        $assignValue("apiURL");
        $assignValue("uiURL");
        $assignValue("describoSecretKey");
        $assignValue("oauthname");
        $assignValue("documentation");

        return $t;
    }

    public function getPriority()
    {
        return 20;
    }
}
