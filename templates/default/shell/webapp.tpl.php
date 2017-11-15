<?php

    $icons = Idno\Core\site()->getSiteIcons();
    $page_icon = $icons['page'];
    $page_icon_mime = (strpos($page_icon, '.jpg') !== false) ? 'image/jpg' : 'image/png';
 
?>

<!-- Make this an "app" when saved to an ios device's home screen -->
<link rel="apple-touch-icon-precomposed" href="<?= $page_icon ?>">
<!-- Make this an "app" when saved to an ios device's home screen -->
<link rel="apple-touch-icon" href="<?=$page_icon?>">
<!-- <meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black"> -->

<!-- Android -->
<link rel="manifest" href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>chrome/manifest.json">

<?php
if (Idno\Core\site()->isSecure()) {
    ?>
        <!-- <script>
            window.addEventListener('load', function () {
                if ('serviceWorker' in navigator) {
                    navigator.serviceWorker.register('<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>chrome/service-worker.js', {scope: '/'})
                        .then(function (r) {
                            console.log('Registered service worker');
                        })
                        .catch(function (whut) {
                            console.error('Could not register service worker');
                            console.error(whut);
                        });
                }
            });
        </script> -->
    <?php
}
