<?php

namespace OCA\Describo;

### EDIT BELOW ###

# Owncloud will use this url to exchange the user information against a sessionId.
# This url have to be available for the ownCloud instance through local network.
const apiURL = "http://api:8080/session/application";

# Owncloud will use this url as the iframe source.
# This url have to be available for the user of ownCloud / describo from public network.
const uiURL = "http://localhost:9000/application";

# This have to be the same secret, which you specified in describo configuration
const describoSecretKey = "IAMSECRET";

# This field can be changed, if you do not want to use the default oauth2 name for describo client.
const oauthname = "describo";

# This field sets the url, which will be opened, when you click on the "i" on the describo admin page next to the site title.
# Helpful for shortcuts.
const documentation = "https://github.com/Arkisto-Platform/describo-online";

### EDIT ABOVE ###
