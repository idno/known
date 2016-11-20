<?php

    header('Content-type: text/html');
    header("Access-Control-Allow-Origin: *");

    // Some clickjacking defence (and to quiet ModSecurity)
    // https://www.owasp.org/index.php/Clickjacking_Defense_Cheat_Sheet
    header("X-Frame-Options: SAMEORIGIN");
