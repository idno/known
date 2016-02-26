<?php

    if (file_exists('../config.ini')) {
        if ($config = @parse_ini_file('../config.ini')) {
            if (!empty($config)) {
                header('Location: ../'); exit;
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
