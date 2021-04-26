<?php

/** @var \OCP\IL10N $l */
/** @var array $_ */
script('describo', 'settings-admin');
?>

<div id="describoSettings" class="section">
    <h2 class="app-name has-documentation"><?php p($l->t('Describo')); ?></h2>

    <a target="_blank" rel="noreferrer" class="icon-info" title="<?php p($l->t('Open documentation')); ?>" name="documentation" href="<?php print_unescaped($_["documentation"]) ?>"></a>

    <form id="describo_settings">
        <p>
            <input type="text" name="apiURL" id="api_url" class="text" <?php if (!empty($_["apiURL"])) { ?> value="<?php print_unescaped($_['apiURL']); ?>" <?php } ?> placeholder="<?php p($l->t('url to api')); ?>" />
            <label for="apiURL">
                <?php p($l->t('the api URL for your describo instance. Needs to be available through owncloud networks.')); ?>
            </label>
        </p>
        <p>
            <input type="text" name="uiURL" id="ui_url" class="text" <?php if (!empty($_["uiURL"])) { ?> value="<?php print_unescaped($_['uiURL']); ?>" <?php } ?> placeholder="<?php p($l->t('url for iframe source')); ?>" />
            <label for="uiURL">
                <?php p($l->t('the ui URL for your describo instance. Needs to be available through public networks.')); ?>
            </label>
        </p>
        <p>
            <input type="text" name="describoSecretKey" id="describo_secret_key" class="text" <?php if (!empty($_["describoSecretKey"])) { ?> value="<?php print_unescaped($_['describoSecretKey']); ?>" <?php } ?> placeholder="<?php p($l->t('secret key for authentication')); ?>" />
            <label for="describoSecretKey">
                <?php p($l->t('the secret for your describo instance, which you set in the configuration')); ?>
            </label>
        </p>
        <p>
            <input type="text" name="oauthname" id="oauth_name" class="text" <?php if (!empty($_["oauthname"])) { ?> value="<?php print_unescaped($_['oauthname']); ?>" <?php } ?> placeholder="<?php p($l->t('name of oauth client')); ?>" />
            <label for="oauthname">
                <?php p($l->t('the name of the oauth client, which you entered in your ownCloud instance for describo authorization. It needs to be the same.')); ?>
            </label>
        </p>
        <input id="describo_submit" type="button" class="button" value="<?php p($l->t('Save')); ?>">
        <span class="msg"></span>
    </form>
    </p>
</div>