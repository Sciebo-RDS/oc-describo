<?php

namespace OCA\Describo\Panels;

use \OCA\OAuth2\Db\ClientMapper;
use OCP\IURLGenerator;
use OCP\IUserSession;
use OCP\Settings\ISettings;
use OCP\Template;
use OCP\IConfig;


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
        $userId = $this->userSession->getUser()->getUID();
        $t = new Template($this->appName, 'settings-admin');
        $t->assign("cloudURL", $this->config->getAppValue($this->appName, "cloudURL", "http://localhost:8080"));
        $t->assign("oauthname", $this->config->getAppValue($this->appName, "oauthname", "describo"));
        return $t;
    }

    public function getPriority()
    {
        return 20;
    }
}
