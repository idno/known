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
            echo "<p>If you like, you can <a href=\"mailto:hello@withknown.com?subject=" .
                rawurlencode("Fatal error in Known install at {$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}") . "&body=" . rawurlencode($error_message) . "\">email us for more information</a>.";

            if (isset(\Idno\Core\Idno::site()->logging) && \Idno\Core\Idno::site()->logging)
                \Idno\Core\Idno::site()->logging->log($error_message, LOGLEVEL_ERROR);
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
    if (ini_get('max_execution_time') < 120) {
        set_time_limit(120);
    }

// Host for the purposes of extra paths
    if (!empty($_SERVER['HTTP_HOST'])) {
        $host = strtolower($_SERVER['HTTP_HOST']);
        $host = str_replace('www.', '', $host);
        define('KNOWN_MULTITENANT_HOST', $host);
    }

    require __DIR__.'/../vendor/autoload.php';

// Shims
    include 'shims.php';

// Register the idno-templates folder as the place to look for templates in Bonita
    \Bonita\Main::additionalPath(dirname(dirname(__FILE__)));

// Init main system classes

    $idno         = new Idno\Core\Idno();
    $account      = new Idno\Core\Account();
    $admin        = new Idno\Core\Admin();
    $webfinger    = new Idno\Core\Webfinger();
    $webmention   = new Idno\Core\Webmention();
    $pubsubhubbub = new Idno\Core\PubSubHubbub();
