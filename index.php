<?php

    /**
     * Known index page and router.
     * It all starts here!
     *
     * If you're wondering what this is all about, you could do worse than
     * check out the README.md file.
     *
     * Project homepage:    https://withknown.com/
     * Project repo:        https://github.com/idno/known
     *
     * @package idno
     * @subpackage core
     */

// Check PHP version first of all
    if (version_compare(phpversion(), '5.4', '<')) {
        header('Location: warmup/'); exit;
    }

// Load the idno framework

    require_once(dirname(__FILE__) . '/Idno/start.php');

// Get page routes

    $routes = \Idno\Core\Idno::site()->pagehandlers;

// Get subdirectory

    $url = \Idno\Core\Idno::site()->config()->getURL();
    $path = parse_url($url, PHP_URL_PATH);
    if(substr($path, -1) == '/') {
        $path = substr($path, 0, -1);
    }
    if (!empty($path)) {
        if (!empty($routes['/'])) {
            $routes[$path . '/'] = $routes['/'];
        }
    }

// Manage routing

    \Idno\Core\PageHandler::hook('404', function ($params = array()) {
        http_response_code(404);
        $t = \Idno\Core\Idno::site()->template();

        // Take over page detection
        \Idno\Core\Idno::site()->template()->autodetectTemplateType();

        (new \Idno\Pages\Homepage())->noContent();
     });
    \Idno\Core\PageHandler::serve($routes);