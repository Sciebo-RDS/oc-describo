<?php
style('describo', array('style'));
?>

<noscript>
    <strong>
        We're sorry but describo doesn't work properly without JavaScript enabled. Please enable it to
        continue.
    </strong>
</noscript>
<div id="owncloud-rds-app">
    <iframe id="describo-iframe" src="<?php print_unescaped($_['iframeSource']); ?>" style="width: 100%; height: 100%">
        No iframes supported.
    </iframe>
</div>