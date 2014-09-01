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

            $error_message = "Fatal Error: {$error['file']}:{$error['line']} - \"{$error['message']}\", on page {$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}";

            echo "<h1>Oh no! Known experienced a problem!</h1>";
            echo "<p>Known experienced a problem with this page and couldn't continue. The technical details are as follows:</p>";
            echo "<pre>$error_message</pre>";
            echo "<p>We've logged this error and will make sure we take a look.</p>";
            echo "<p>If you like, you can <a href=\"mailto:hello@withknown.com?subject=" .
                rawurlencode("Fatal error in Known install at {$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}") . "&body=" . rawurlencode($error_message) . "\">email us for more information</a>.";

            \Idno\Core\site()->logging->log($error_message, LOGLEVEL_ERROR);

            exit;
        }
    });

// Set time limit if we're using the default
    if (ini_get('max_execution_time') == 30) {
        set_time_limit(120);
    }

// We're making heavy use of the Symfony ClassLoader to load our classes
    require_once(dirname(dirname(__FILE__)) . '/external/Symfony/Component/ClassLoader/UniversalClassLoader.php');
    $loader = new \Symfony\Component\ClassLoader\UniversalClassLoader();

// Register our main namespaces (all idno classes adhere to the PSR-0 standard)

// idno trunk classes (i.e., the main framework) are in /idno
    $loader->registerNamespace('Idno', dirname(dirname(__FILE__)));
// Host for the purposes of extra paths
    if (!empty($_SERVER['HTTP_HOST'])) {
        $host = strtolower($_SERVER['HTTP_HOST']);
        $host = str_replace('www.', '', $host);
        define('KNOWN_MULTITENANT_HOST', $host);
// idno plugins are located in /IdnoPlugins and must have their own namespace
        $loader->registerNamespace('IdnoPlugins', [dirname(dirname(__FILE__)), dirname(dirname(__FILE__)) . '/hosts/' . $host]);
// idno themes are located in /Themes and must have their own namespace
        $loader->registerNamespace('Themes', [dirname(dirname(__FILE__)), dirname(dirname(__FILE__)) . '/hosts/' . $host]);
    }

// Register our external namespaces (PSR-0 compliant modules that we love, trust and need)

// Bonita is being used for templating
    $loader->registerNamespace('Bonita', dirname(dirname(__FILE__)) . '/external/bonita/includes');
// Symfony is used for routing, observer design pattern support, and a bunch of other fun stuff
    $loader->registerNamespace('Symfony\Component', dirname(dirname(__FILE__)) . '/external');

// Using Toro for URL routing
    require_once(dirname(dirname(__FILE__)) . '/external/torophp/src/Toro.php');

// Using mf2 for microformats parsing, and webignition components to support it
    $loader->registerNamespace('webignition\Url', dirname(dirname(__FILE__)) . '/external/webignition/url/src');
    $loader->registerNamespace('webignition\AbsoluteUrlDeriver', dirname(dirname(__FILE__)) . '/external/webignition/absolute-url-deriver/src');
    $loader->registerNamespace('webignition\NormalisedUrl', dirname(dirname(__FILE__)) . '/external/webignition/url/src');
    $loader->registerNamespace('Mf2', dirname(dirname(__FILE__)) . '/external/mf2');

// Register the autoloader
    $loader->register();

// Register the idno-templates folder as the place to look for templates in Bonita
    \Bonita\Main::additionalPath(dirname(dirname(__FILE__)));

// Not sure if this is the way we should be initializing everything yet.
// TODO: do this more intelligently.

    $idno         = new Idno\Core\Idno();
    $account      = new Idno\Core\Account();
    $admin        = new Idno\Core\Admin();
    $webfinger    = new Idno\Core\Webfinger();
    $webmention   = new Idno\Core\Webmention();
    $pubsubhubbub = new Idno\Core\PubSubHubbub();
