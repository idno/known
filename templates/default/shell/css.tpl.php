<link href="<?php echo \Idno\Core\Idno::site()->config()->getStaticURL() ?>css/known.min.css" rel="stylesheet">

<!-- Syndication -->
<link
    href="<?php echo \Idno\Core\Idno::site()->config()->getStaticURL() ?>vendor/npm-asset/bootstrap-toggle/css/bootstrap-toggle.min.css"
    rel="stylesheet"/>

<!-- Mention styles -->
<link rel="stylesheet" type="text/css"
      href="<?php echo \Idno\Core\Idno::site()->config()->getStaticURL() ?>vendor/idno/mentionjs/recommended-styles.css">

<link rel="stylesheet" type="text/css"
      href="<?php echo \Idno\Core\Idno::site()->config()->getStaticURL() ?>vendor/npm-asset/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css">

<?php
    // Load style assets
if ((\Idno\Core\Idno::site()->currentPage()) && $style = \Idno\Core\Idno::site()->currentPage()->getAssets('css')) {
    foreach ($style as $css) {
        ?>
            <link href="<?php echo $css; ?>" rel="stylesheet">
            <?php
    }
}
