<?php

    $user_avatar_favicons = \Idno\Core\site()->config('user_avatar_favicons');
    if ((\Idno\Core\site()->currentPage()) && ($user = \Idno\Core\site()->currentPage()->getOwner())) {
        if ($user instanceof \Idno\Entities\User) {
            $user_icon = $user->getIcon();
            if (strpos($icon, 'thumb.jpg') !== false) {
                $user_icon_mime = 'image/jpg';
            } else {
                $user_icon_mime = 'image/png';
            }
            if ($user_avatar_favicons) {
                $icon      = $user_icon;
                $icon_mime = $user_icon_mime;
            }
        } else {
            $user_icon      = \Idno\Core\site()->config()->getURL() . 'gfx/logos/logo_k.png';
            $user_icon_mime = 'image/png';
        }
    }

?>
<link rel="shortcut icon" type="<?= $icon_mime ?>" href="<?= $icon ?>">
<!-- Make this an "app" when saved to an ios device's home screen -->
<link rel="apple-touch-icon-precomposed" href="<?= $user_icon ?>">
<!-- Make this an "app" when saved to an ios device's home screen -->
<link rel="apple-touch-icon" href="<?=$user_icon?>">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">