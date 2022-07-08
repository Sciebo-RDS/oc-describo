<?php

$policy = new OCP\AppFramework\Http\EmptyContentSecurityPolicy();
$policy->addAllowedFrameDomain(\OC::$server->getConfig()->getAppValue("describo", "uiURL", "http://localhost:9000/application"));

\OC::$server->getContentSecurityPolicyManager()->addDefaultPolicy($policy);
// https://gist.github.com/butonic/4e6d050b778866e3aa99af14d9474613

\OC::$server->getNavigationManager()->add(function () {
    $urlGenerator = \OC::$server->getURLGenerator();
    return [
        // The string under which your app will be referenced in owncloud
        'id' => 'describo',

        // The sorting weight for the navigation.
        // The higher the number, the higher will it be listed in the navigation
        'order' => 200,

        // The route that will be shown on startup
        'href' => $urlGenerator->linkToRoute('describo.page.index'),

        // The icon that will be shown in the navigation, located in img/
        'icon' => $urlGenerator->imagePath('describo', 'research-white.svg'),

        // The application's title, used in the navigation & the settings page of your app
        'name' => \OC::$server->getL10N('describo')->t('Describo'),
    ];
});


use OCP\Util;

$eventDispatcher = \OC::$server->getEventDispatcher();
Util::addStyle("describo", "style");
$eventDispatcher->addListener('OCA\Files::loadAdditionalScripts', function () {
    # TODO: Add here describo filemenu stuff.
    Util::addScript('describo', 'fileActions');
});
