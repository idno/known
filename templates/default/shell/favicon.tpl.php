<?php

    $icons = Idno\Core\site()->getSiteIcons();
    $page_icon = $icons['page'];
    $page_icon_mime = (strpos($page_icon, '.jpg') !== false) ? 'image/jpg' : 'image/png';
 
?>

<link rel="shortcut icon" type="<?= $page_icon_mime ?>" href="<?= $page_icon ?>">