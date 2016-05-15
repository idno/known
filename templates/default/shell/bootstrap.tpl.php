<!-- We need jQuery at the top of the page -->
<script src="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() . 'external/jquery/' ?>jquery.min.js"></script>

<!-- Le styles -->
<link
    href="<?= \Idno\Core\Idno::site()->config()->getStaticURL() . 'external/bootstrap/' ?>assets/css/bootstrap.min.css"
    rel="stylesheet"/>
<link
    href="<?= \Idno\Core\Idno::site()->config()->getStaticURL() . 'external/bootstrap/' ?>assets/css/bootstrap-theme.min.css"/>
<script
    src="<?= \Idno\Core\Idno::site()->config()->getStaticURL() . 'external/bootstrap/' ?>assets/js/bootstrap.min.js"></script>

<!-- Accessibility -->
<link rel="stylesheet"
      href="<?= \Idno\Core\Idno::site()->config()->getStaticURL() . 'external/paypal-bootstrap-accessibility-plugin/' ?>plugins/css/bootstrap-accessibility_1.0.3.css">
<script
    src="<?= \Idno\Core\Idno::site()->config()->getStaticURL() . 'external/paypal-bootstrap-accessibility-plugin/' ?>plugins/js/bootstrap-accessibility_1.0.3.min.js"></script>

<!-- Fonts -->
<link rel="stylesheet"
      href="<?= \Idno\Core\Idno::site()->config()->getStaticURL() ?>external/font-awesome/css/font-awesome.css">
<!--<link rel="stylesheet"
          href="<?= \Idno\Core\Idno::site()->config()->getStaticURL() ?>external/font-awesome/css/font-awesome.min.css">-->

<style>
    body {
        padding-top: 100px; /* 60px to make the container go all the way to the bottom of the topbar */
    }
</style>

<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script
    src="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() . 'external/bootstrap/' ?>assets/js/html5shiv.js"></script>
<![endif]-->
