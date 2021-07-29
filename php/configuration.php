<?php

namespace OCA\Describo;

### EDIT BELOW ###

# Owncloud will use this url to exchange the user information against a sessionId.
# This url have to be available for the ownCloud instance through local network.
define("OCA\Describo\apiURL", getenv("DESCRIBO_API_URL") ??  "http://api:8080/session/application");

# Define the URL for the Describo API to talk to the owncloud service internally 
define("OCA\Describo\internalOwncloudURL", getenv("OWNCLOUD_INTERNAL_URL") ?? "http://owncloud_server:8080");

# Owncloud will use this url as the iframe source.
# This url have to be available for the user of ownCloud / describo from public network.
define("OCA\Describo\uiURL", getenv("DESCRIBO_APP_URL") ?? "http://localhost:9000/application");

# You need to specify the url for e.g. oauth2 workflow, so the browser allows the redirection.
define("OCA\Describo\oauthProvidersURL", [getenv("OWNCLOUD_URL") ?? "http://localhost:8000"]);

# This have to be the same secret, which you specified in describo configuration
define("OCA\Describo\describoSecretKey", getenv("DESCRIBO_APP_SECRET") ?? "IAMSECRET");

# This field can be changed, if you do not want to use the default oauth2 name for describo client.
const oauthname = "describo";

# This field sets the url, which will be opened, when you click on the "i" on the describo admin page next to the site title.
# Helpful for shortcuts.
const documentation = "https://github.com/Arkisto-Platform/describo-online";

### EDIT ABOVE ###
