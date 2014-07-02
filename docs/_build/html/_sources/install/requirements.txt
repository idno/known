System requirements
###################

Known *requires* the following server components:

* A Web Server that supports URL rewriting (Apache + mod_rewrite recommended)
* PHP 5.4 or above
* MongoDB or MySQL 5

Additionally, Known requires the following PHP components:

* curl
* date
* dom
* fileinfo
* gd
* intl
* json
* libxml
* mbstring
* mongo or mysql (depending on database backend)
* oauth
* reflection
* session

Note that you may need to restart the web server after installing these components. Known's installer will tell you
if a required module isn't available.

Recommendations
---------------

Idno *recommends* the following extra server components:

* Linux or UNIX-based server
* A PHP accelerator like eAccelerator
* A secure certificate (so connections to Known can be made secure)
* A server cache like Squid
