<div id="syndication" style="display:none"></div>
<script>
    $('#syndication').load("<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>withknown/syndication?content_type=<?=urlencode($vars['content_type'])?>", function() {
        $('#syndication').show();
        $('.syndication-toggle input[type=checkbox]').bootstrapToggle();
    });
</script>
