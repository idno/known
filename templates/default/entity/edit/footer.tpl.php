<?= $this->draw('js/mentions') ?>
<script>
    /* Handle submit button */
    $(document).ready(function() {
        $('div.page-body form').submit(function() {
            $('input[type=submit]').attr('disabled', 'yes').val('Saving').removeClass('btn-primary').addClass('btn-link');
            $('.btn-cancel').hide();
        });
    });
</script>