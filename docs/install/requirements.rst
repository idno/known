System requirements
###################

Known *requires* the following server components:

* A Web Server that supports URL rewriting (Apache + mod_rewrite recommended)
* If you are using Apache, you also need to make sure support for .htaccess is enabled (using `the AllowOverride All directive <https://help.ubuntu.com/community/EnablingUseOfApacheHtaccessFiles>`_).
* PHP 5.4 or above
* MongoDB or MySQL 5

Known must be installed at the root of a domain, and does not currently support subdirectory installations. It does
support subdomains, however.

If you use Apache 2.4, you either must install and activate:
* mod_access_compat (see http://httpd.apache.org/docs/2.4/mod/mod_access_compat.html)
or manually edit Known's stock .htaccess file by replacing::

    <Files ~ "\.ini$">
    Order allow,deny
    Deny from all
    </Files>
    <Files ~ "\.xml$">
    Order allow,deny
    Deny from all
    </Files>

with::

    <Files ~ "\.ini$">
    Require all denied
    </Files>
    <Files ~ "\.xml$">
    Require all denied
    </Files>

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
* xmlrpc

Note that you may need to restart the web server after installing these components. Known's installer will tell you
if a required module isn't available.

Recommendations
---------------

Known *recommends* the following extra server components:

* Linux or UNIX-based server
* A PHP accelerator like eAccelerator
* A secure certificate (so connections to Known can be made secure)
* A server cache like Squid
