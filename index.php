<?php

/**
 * Known index page and router.
 * It all starts here!
 *
 * If you're wondering what this is all about, you could do worse than
 * check out the README.md file.
 *
 * Project homepage:    http://withknown.com/
 * Project repo:        https://github.com/known/known
 *
 * @package known
 * @subpackage core
 */

// Load the known framework

require_once(dirname(__FILE__) . '/known/start.php');

// Manage routing

\known\Core\PageHandler::hook('404', function () {
    http_response_code(404);
    $t = \known\Core\site()->template();
    $t->__(['body' => $t->draw('pages/404'), 'title' => 'Not found!'])->drawPage();
    exit;
});
\known\Core\PageHandler::serve(\known\Core\site()->pagehandlers);