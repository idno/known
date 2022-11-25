OAuth2 Server for Known
=======================

** EXPERIMENTAL **

This plugin provides an OAuth2 Server for Known, allowing users to create applications 
and allow clients to authenticate themselves for the API and website using an OAuth2 access token.

This plugin is an experimental basic implementation of the spec, so please, kick it around and report 
any issues you find!

Usage
-----

* Install in your plugins
* Create an application via settings
* Use the appropriate keys in your OAuth2 client

Support
-------
Currently the plugin supports:

* [x] response_type=code
* [x] grant_type=authorization_code
* [x] grant_type=refresh_token
* [x] grant_type=password
* [x] state parameter validation
* [x] scope support
* [x] OpenID Connect

Example usage
-------------

**To get a code:**

```https://mysite.com/oauth2/authorise/?response_type=code&client_id=<your API Key>&redirect_uri=<path to your endpoint>```

You will be bounced to a login + authorisation page if necessary, so follow forwards.

As per the spec, you can omit the ```redirect_uri```, in which case the response will be a straight json encoded blob. If ```redirect_uri``` is specified you will be
forwarded to the endpoint, with appropriate parameters in the GET fields.


**To get a token:**

```https://mysite.com/oauth2/access_token/?grant_type=authorization_code&client_id=<your API Key>&redirect_uri=<path to your endpoint>```

You should get back a json encoded blob with an access token, expiry and refresh token.


**To refresh a token:**

If your access token has expired, you can update it with the refresh token.

```https://mysite.com/oauth2/access_token/?grant_type=refresh_token&refresh_token=<refresh token>```

Success will spit back a new access token, refresh token and expiry. It also results in the destruction of the original token.


Accessing the token
-------------------

On a successful login the token used will be saved to the current session in ```$_SESSION['oauth2_token']```, you can use this to check scope permissions, application ID and other details.

The scope granted to a given user is also saved against the user object in an array ```$user->oauth2[$client_id]['scope']```, which is also cross checked on login.

Why not use native signed HTTP?
-------------------------------

Natively, Known uses a per-user api key to sign requests, so why not use this? 

Of course you can still, and the OAuth2 server doesn't replace that option. In many ways the signed HTTP approach is easier to get going, however...

1) There are many existing libraries for OAuth2 in pretty much every language.
2) With OAuth2 you give different credentials to each application. This means that if you no longer want to allow access from application A, but still want to keep B and C, you can revoke A's tokens specifically.

OpenID Connect
--------------

If you include the scope `openid`, on success the server will return an OpenID Connect signed JWT in the `id_token` field. 

This token will include basic information about the authenticated user. If you also ask for `email` and `profile` scopes as well, you'll get some extra profile information back (email, full name, picture url, username, etc). 

You can verify this token against the public key for the application (available from `https://yourserver.com/oauth2/CLIENTID/key`)

See
---
 * Author: Marcus Povey <http://www.marcus-povey.co.uk> 
 * OAuth2 Spec <https://tools.ietf.org/html/rfc6749>
