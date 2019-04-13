<?php echo $this->draw('js/known'); ?>

<!-- Default Known JavaScript -->
<script src="<?php echo \Idno\Core\Idno::site()->config()->getStaticURL() ?>js/<?php echo $this->getModifiedTS('js/known.min.js'); ?>/known.min.js"></script>

<script
    src="<?php echo \Idno\Core\Idno::site()->config()->getStaticURL() ?>vendor/npm-asset/bootstrap-toggle/js/bootstrap-toggle.js"></script>

