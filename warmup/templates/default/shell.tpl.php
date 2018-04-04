<?php
    if (empty($vars['title']))
        $vars['title'] = 'Welcome to Known';
?>
<!doctype html>
<html>
    <head>
        <title><?= htmlspecialchars($vars['title']); ?></title>
        <meta name="robots" content="noindex, nofollow">
        <link rel="stylesheet" href="../css/simple.css">
    </head>
    <body>
        <?= $vars['body']; ?>
    </body>
</html>