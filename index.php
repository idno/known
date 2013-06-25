<?php

/**
 * idno index page and router.
 * It all starts here!
 *
 * If you're wondering what this is all about, you could do worse than
 * check out the README.md file.
 *
 * Project homepage:    http://idno.co/
 * Project repo:        https://github.com/idno/idno
 *
 * @package idno
 * @subpackage core
 */

// Load the idno framework

require_once(dirname(__FILE__) . '/Idno/start.php');

// Manage routing

\Idno\Core\PageHandler::hook('404', function () {

});
\Idno\Core\PageHandler::serve(\Idno\Core\site()->pagehandlers);