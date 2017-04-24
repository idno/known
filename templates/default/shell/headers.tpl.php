<?php

    header('Content-type: text/html');
    header("Access-Control-Allow-Origin: *");

    $page = \Idno\Core\Idno::site()->currentPage();
    if (!empty($page)) {
        $page = $page->currentUrl(true);
        
        if (strpos($page['path'], '/share?')===false) {
            // Some clickjacking defence (and to quiet ModSecurity)
            // https://www.owasp.org/index.php/Clickjacking_Defense_Cheat_Sheet
            header("X-Frame-Options: SAMEORIGIN");
        }
    }
