<?php

    $user_avatar_favicons = \known\Core\site()->config('user_avatar_favicons');
    if (($user = \known\Core\site()->currentPage()->getOwner()) && ($user_avatar_favicons)) {
        if ($user instanceof \known\Entities\User) {
            $icon = $user->getIcon();
            if (strpos($icon, 'thumb.jpg') !== false) {
                $icon_mime = 'image/jpg';
            } else {
                $icon_mime = 'image/png';
            }
        }

?>
        <link rel="shortcut icon" type="<?=$icon_mime?>" href="<?=$icon?>">

        <!-- Make this an "app" when saved to an ios device's home screen -->
        <link rel="apple-touch-icon-precomposed" href="<?=$icon?>">
<?php
    } else {
?>
<!--        <link rel="icon" type="image/png" href="<?=\known\Core\site()->config()->url?>gfx/template/favicon.png"> -->
        <link rel="icon" type="image/png" href="http://withknown.com/img/logo_k.png">
        <!-- Make this an "app" when saved to an ios device's home screen -->
        <link rel="apple-touch-icon" href="http://withknown.com/img/logo_k.png">
<?php
    }

?>
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">