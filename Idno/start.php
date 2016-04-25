<?php

    /**
     * Known loader and all-purpose conductor
     *
     * @package idno
     * @subpackage core
     */

// Register a function to catch premature shutdowns and output some friendlier text
    register_shutdown_function(function () {
        $error = error_get_last();
        if ($error["type"] == E_ERROR) {

            ob_clean();

            http_response_code(500);

            if (!empty($_SERVER['SERVER_NAME'])) {
                $server_name = $_SERVER['SERVER_NAME'];
            } else {
                $server_name = '';
            }
            if (!empty($_SERVER['REQUEST_URI'])) {
                $request_uri = $_SERVER['REQUEST_URI'];
            } else {
                $request_uri = '';
            }

            $error_message = "Fatal Error: {$error['file']}:{$error['line']} - \"{$error['message']}\", on page {$server_name}{$request_uri}";

            echo "<h1>Oh no! Known experienced a problem!</h1>";
            echo "<p>Known experienced a problem with this page and couldn't continue. The technical details are as follows:</p>";
            echo "<pre>$error_message</pre>";

            if (file_exists(dirname(dirname(__FILE__)) . '/support.inc')) {
                include dirname(dirname(__FILE__)) . '/support.inc';
            } else {
                echo '<p>If you continue to have problems, <a href="https://withknown.com/opensource" target="_blank">open source users have a number of resources available</a></p>.';
            }

            if (isset(\Idno\Core\Idno::site()->logging) && \Idno\Core\Idno::site()->logging)
                \Idno\Core\Idno::site()->logging->error($error_message);
            else
                error_log($error_message);

            exit;
        }
    });

// This is a good time to see if we're running in a subdirectory
    if (!defined('KNOWN_UNIT_TEST')) {
        if (!empty($_SERVER['PHP_SELF'])) {
            if ($subdir = dirname($_SERVER['PHP_SELF'])) {
                if ($subdir != DIRECTORY_SEPARATOR) {
                    if (substr($subdir, -1) == DIRECTORY_SEPARATOR) {
                        $subdir = substr($subdir, 0, -1);
                    }
                    if (substr($subdir, 0, 1) == DIRECTORY_SEPARATOR) {
                        $subdir = substr($subdir, 1);
                    }
                    $subdir = str_replace(DIRECTORY_SEPARATOR, '/', $subdir);
                    define('KNOWN_SUBDIRECTORY', $subdir);
                }
            }
        }
    }

// Set time limit if we're using less
    if (ini_get('max_execution_time') < 120 && ini_get('safe_mode')) {
        set_time_limit(120);
    }

// We're making heavy use of the Symfony ClassLoader to load our classes
    require_once(dirname(dirname(__FILE__)) . '/external/Symfony/Component/ClassLoader/UniversalClassLoader.php');
    global $known_loader;
    $known_loader = new \Symfony\Component\ClassLoader\UniversalClassLoader();

    /**
     * Retrieve the loader
     * @return \Symfony\Component\ClassLoader\UniversalClassLoader
     */
    function &loader()
    {
        global $known_loader;

        return $known_loader;
    }

// Register our main namespaces (all idno classes adhere to the PSR-0 standard)

// idno trunk classes (i.e., the main framework) are in /idno
    $known_loader->registerNamespace('Idno', dirname(dirname(__FILE__)));
// Host for the purposes of extra paths
    if (!empty($_SERVER['HTTP_HOST'])) {
        $host = strtolower($_SERVER['HTTP_HOST']);
        $host = str_replace('www.', '', $host);
        define('KNOWN_MULTITENANT_HOST', $host);
// idno plugins are located in /IdnoPlugins and must have their own namespace
        $known_loader->registerNamespace('IdnoPlugins', array(dirname(dirname(__FILE__)), dirname(dirname(__FILE__)) . '/hosts/' . $host));
// idno themes are located in /Themes and must have their own namespace
        $known_loader->registerNamespace('Themes', array(dirname(dirname(__FILE__)), dirname(dirname(__FILE__)) . '/hosts/' . $host));
    }

// Shims
    include 'shims.php';

// Register our external namespaces (PSR-0 compliant modules that we love, trust and need)

// Bonita is being used for templating
    $known_loader->registerNamespace('Bonita', dirname(dirname(__FILE__)) . '/external/bonita/includes');
// Symfony is used for routing, observer design pattern support, and a bunch of other fun stuff
    $known_loader->registerNamespace('Symfony\Component', dirname(dirname(__FILE__)) . '/external');

// Implement the PSR-3 logging interface
    $known_loader->registerNamespace('Psr\Log', dirname(dirname(__FILE__)) . '/external/log');

// Using Toro for URL routing
    require_once(dirname(dirname(__FILE__)) . '/external/torophp/src/Toro.php');

// Using mf2 for microformats parsing, and webignition components to support it
    $known_loader->registerNamespace('webignition\Url', dirname(dirname(__FILE__)) . '/external/webignition/url/src');
    $known_loader->registerNamespace('webignition\AbsoluteUrlDeriver', dirname(dirname(__FILE__)) . '/external/webignition/absolute-url-deriver/src');
    $known_loader->registerNamespace('webignition\NormalisedUrl', dirname(dirname(__FILE__)) . '/external/webignition/url/src');
    $known_loader->registerNamespace('Mf2', dirname(dirname(__FILE__)) . '/external/mf2');
    $known_loader->registerNamespace('IndieWeb', dirname(dirname(__FILE__)) . '/external/mention-client-php/src');

// Using Simplepie for RSS and Atom parsing
    include dirname(dirname(__FILE__)) . '/external/simplepie/autoloader.php';

// Using HTMLPurifier for HTML sanitization
    include dirname(dirname(__FILE__)) . '/external/htmlpurifier-lite/library/HTMLPurifier.auto.php';

// Register the autoloader
    $known_loader->register();

// Register the idno-templates folder as the place to look for templates in Bonita
    \Bonita\Main::additionalPath(dirname(dirname(__FILE__)));

// Init main system classes

    $idno         = new Idno\Core\Idno();
    $account      = new Idno\Core\Account();
    $admin        = new Idno\Core\Admin();
    $webfinger    = new Idno\Core\Webfinger();
    $webmention   = new Idno\Core\Webmention();
    $pubsubhubbub = new Idno\Core\PubSubHubbub();
