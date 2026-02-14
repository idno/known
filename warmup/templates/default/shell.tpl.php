<?php
if (empty($vars['title'])) {
    $vars['title'] = 'Welcome to Idno';
}
?>
<!doctype html>
<html>
    <head>
        <title><?php echo htmlspecialchars($vars['title']); ?></title>
        <meta name="robots" content="noindex, nofollow">
        <link rel="stylesheet" href="../css/idno-simple.min.css">
    </head>
    <body>
        <?php echo $vars['body']; ?>
    </body>
</html>