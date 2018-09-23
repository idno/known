<?php
$icons = Idno\Core\site()->getSiteIcons();
$page_icon = $icons['page'];
$page_icon_mime = (strpos($page_icon, '.jpg') !== false) ? 'image/jpg' : 'image/png';
?>

<!-- Make this an "app" when saved to an ios device's home screen -->
<link rel="apple-touch-icon-precomposed" href="<?php echo $page_icon ?>">
<!-- Make this an "app" when saved to an ios device's home screen -->
<link rel="apple-touch-icon" href="<?php echo $page_icon ?>">
<!-- <meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black"> -->

<!-- Android -->
<link rel="manifest" href="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL() ?>chrome/manifest.json">

<?php
if (\Idno\Common\Page::isSSL() && \Idno\Core\Idno::site()->session()->isLoggedIn()) {
    ?>
    <script>
        window.addEventListener('load', function () {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL() ?>service-worker.min.js', {
                scope: '<?php
                    // Work out scope
                    $url = parse_url(\Idno\Core\Idno::site()->config()->getDisplayURL());
                    if (empty($url['path']))
                        echo '/';
                else
                        echo $url['path'];
                ?>'
            })
                .then(function (r) {
                console.log('Registered service worker');
                })
                .catch(function (whut) {
                console.error('Could not register service worker');
                console.error(whut);
                });
        }
        });
    </script>
    <?php
}
