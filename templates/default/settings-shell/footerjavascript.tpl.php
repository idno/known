
<!-- Placed at the end of the document so the pages load faster -->

<?php

if (\Idno\Core\Idno::site()->session()->isLoggedIn()) {

    ?>
        <!-- WYSIWYG editor -->
        <script
            src="<?php echo \Idno\Core\Idno::site()->config()->getStaticURL() ?>vendor/tinymce/tinymce/tinymce.min.js"
            type="text/javascript"></script>
        <script
            src="<?php echo \Idno\Core\Idno::site()->config()->getStaticURL() ?>vendor/tinymce/tinymce/jquery.tinymce.min.js"
            type="text/javascript"></script>
        <?php

}

?>

<script
    src="<?php echo \Idno\Core\Idno::site()->config()->getStaticURL() ?>vendor/rmm5t/jquery-timeago/jquery.timeago.js"></script>
<script src="<?php echo \Idno\Core\Idno::site()->config()->getStaticURL() ?>vendor/npm-asset/underscore/underscore-min.js"
        type="text/javascript"></script>
<!--<script src="<?php echo \Idno\Core\Idno::site()->config()->getStaticURL() . 'vendor/bower-asset/mention/bootstrap-typeahead.js' ?>"
        type="text/javascript"></script>
<script src="<?php echo \Idno\Core\Idno::site()->config()->getStaticURL() . 'vendor/bower-asset/mention/mention.js' ?>"
        type="text/javascript"></script> -->

<?php
if (\Idno\Core\Idno::site()->session()->isLoggedOn()) {
    echo $this->draw('js/mentions');
}
    // Load javascript assets
if ((\Idno\Core\Idno::site()->currentPage()) && $scripts = \Idno\Core\Idno::site()->currentPage->getAssets('javascript')) {
    echo "<!-- Begin asset javascript -->";
    foreach ($scripts as $script) {
        ?>
            <script src="<?php echo $script ?>"></script>
            <?php
    }
    echo "<!-- End asset javascript -->";
}
?>


