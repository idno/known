<?php

    /**
     * Known index page and router.
     * It all starts here!
     *
     * If you're wondering what this is all about, you could do worse than
     * check out the README.md file.
     *
     * Project homepage:    http://withknown.com/
     * Project repo:        https://github.com/idno/idno
     *
     * @package idno
     * @subpackage core
     */

// Load the idno framework

    require_once(dirname(__FILE__) . '/Idno/start.php');

// Get page routes

    $routes = \Idno\Core\site()->pagehandlers;

// Get subdirectory

    $url = \Idno\Core\site()->config()->getURL();
    $path = parse_url($url, PHP_URL_PATH);
    if(substr($path, -1) == '/') {
        $path = substr($path, 0, -1);
    }
    if (!empty($path)) {
        $routes[$path . '/'] = $routes['/'];
    }

// Manage routing

    \Idno\Core\PageHandler::hook('404', function () {
        http_response_code(404);
        $t = \Idno\Core\site()->template();
        $t->__(['body' => $t->draw('pages/404'), 'title' => 'Not found!'])->drawPage();
        exit;
    });
    \Idno\Core\PageHandler::serve($routes);