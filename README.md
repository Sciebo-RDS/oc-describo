# oc-describo

# Installation

## Start / Stop with makefile

This enable oauth2 and describo app within owncloud automatically and create an oauth2 client for describo with preset values.

```bash
make start
make stop
```

## Start / Stop with docker-compose
```bash
docker-compose up -d
docker-compose down
```

### Configuration

If you do not use the makefile (or you want to use it in production), you have to enable the oauth2 and describo app on your own.
This can be done over the [marketplace](https://doc.owncloud.com/server/admin_manual/configuration/server/security/oauth2.html#installation) for oauth2. Describo have to be added manually: You have to copy the php folder to /apps/describo. When you use docker, this folder will be mounted for you.

```bash
cp ./php /var/www/owncloud/apps/describo -r
```

#### Oauth2

If you do not use the makefile (or you want to use it in production), you have to create an oauth2 client on your own.
These can be done in the adminpanel `http://localhost:8000/settings/admin?sectionid=authentication`. Please set `describo` as the name of your new client. Otherwise you have to change the name of your client on the describo admin-panel (described in the next section).

#### Describo

If the describo instance does not run on the default host, you can change it on the describo admin-panel `http://localhost:8000/settings/admin?sectionid=describo`. Also you can change the oauth2 client name, if you do not want to use `describo` as the name.

### Links to know

If you want to get all informations from user, then you can access the following url:
`http://localhost:8000/apps/describo/api/v1/informations`

For jwt validation, you need a publickey. This can be requested here. JWT-Algorithm used RS256:
`http://localhost:8000/apps/describo/api/v1/publickey`

### Getting started

Now, you can open the top-left menu and open the `Describo App`. If everything is correct, you should be redirected to authorize the describo app to access your files. After this, you will be redirect back to your app and the configured iframe source will be shown.

#### Javascript stuff

If you want to request user informations and send it to the describo iframe, you can request within javascript ownCloud namespace with the following codesnippet:

```javascript
$.get(
	OC.generateUrl("/apps/rds/api/1.0/informations")
).done((response) => {
	$("#describo-iframe").contentWindow.postMessage({
        jwt: response.jwt
    })
})
```

### ownCloud stuff

If you want to add the `access_token` to the url, you can add the following snippet in `php/lib/Controller/PageController#index` before the last `return TemplateResponse`:

```php
$iframeUrl .= "?access_token=" . $access_token;
```