
<!-- Placed at the end of the document so the pages load faster -->

<?php

    if (\Idno\Core\Idno::site()->session()->isLoggedIn()) {

        ?>
        <!-- WYSIWYG editor -->
        <script
            src="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>external/tinymce/js/tinymce/tinymce.min.js"
            type="text/javascript"></script>
        <script
            src="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>external/tinymce/js/tinymce/jquery.tinymce.min.js"
            type="text/javascript"></script>
        <?php

    }

?>

<script
    src="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() . 'external/jquery-timeago/' ?>jquery.timeago.js"></script>
<script
    src="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() . 'external/jquery-pjax/' ?>jquery.pjax.js"></script>
<script src="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() . 'external/underscore/underscore-min.js' ?>"
        type="text/javascript"></script>
<!--<script src="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() . 'external/mention/bootstrap-typeahead.js' ?>"
        type="text/javascript"></script>
<script src="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() . 'external/mention/mention.js' ?>"
        type="text/javascript"></script> -->

<?php
    if (\Idno\Core\Idno::site()->session()->isLoggedOn()) {
        echo $this->draw('js/mentions');
    }
    // Load javascript assets
    if ((\Idno\Core\Idno::site()->currentPage()) && $scripts = \Idno\Core\Idno::site()->currentPage->getAssets('javascript')) {
        foreach ($scripts as $script) {
            ?>
            <script src="<?= $script ?>"></script>
            <?php
        }
    }
?>

<!-- HTML5 form element support for legacy browsers -->
<script src="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() . 'external/h5f/h5f.min.js' ?>"></script>

<script src="<?= \Idno\Core\Idno::site()->config()->getStaticURL() . 'js/templates/default/shell.js' ?>"></script>
<script src="<?= \Idno\Core\Idno::site()->config()->getStaticURL() . 'js/embeds.js' ?>"></script>
