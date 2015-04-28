<?php

    $icons = Idno\Core\site()->getSiteIcons();
    $page_icon = $icons['page'];
    $page_icon_mime = (strpos($page_icon, '.jpg') !== false) ? 'image/jpg' : 'image/png';
 
?>
<link rel="shortcut icon" type="<?= $page_icon_mime ?>" href="<?= $page_icon ?>">
<!-- Make this an "app" when saved to an ios device's home screen -->
<link rel="apple-touch-icon-precomposed" href="<?= $page_icon ?>">
<!-- Make this an "app" when saved to an ios device's home screen -->
<link rel="apple-touch-icon" href="<?=$page_icon?>">
<!-- <meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black"> -->