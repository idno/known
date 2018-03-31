<?php

    foreach ([
        dirname(dirname(__FILE__)) . '/config.ini',
        dirname(dirname(__FILE__)) . '/configuration/config.ini'
    ] as $location) {
        if (file_exists($location)) {
            if ($config = @parse_ini_file($location)) {
                if (!empty($config)) {
                    header('Location: ../'); exit;
                }
            }
        }
    }

    if (empty($title)) {
        $title = 'Welcome to Known';
    }

?>
<!doctype html>
<html>
    <head>
        <title><?=htmlspecialchars($title);?></title>
        <meta name="robots" content="noindex, nofollow">
        <link rel="stylesheet" href="../css/simple.css">
    </head>
    <body>
