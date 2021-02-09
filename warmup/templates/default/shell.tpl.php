<?php
if (empty($vars['title'])) {
    $vars['title'] = 'Welcome to Known';
}
?>
<!doctype html>
<html>
    <head>
        <title><?php echo htmlspecialchars($vars['title']); ?></title>
        <meta name="robots" content="noindex, nofollow">
        <link rel="stylesheet" href="../css/known-simple.min.css">
    </head>
    <body>
        <?php echo $vars['body']; ?>
    </body>
</html>