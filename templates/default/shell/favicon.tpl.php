<?php

    $icons = Idno\Core\site()->getSiteIcons();
    $page_icon = $icons['page'];
    
    $mimebits = explode('.', $page_icon);
    $page_icon_mime = 'image/' . end($mimebits);
            
 
?>

<link rel="shortcut icon" type="<?= $page_icon_mime ?>" href="<?= $page_icon ?>">