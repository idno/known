<?= $this->draw('js/mentions') ?>
<script>
    /* Handle submit button */
    $(document).ready(function() {
        $('form').submit(function() {
            $('input[type=submit]').attr('disabled', 'yes').val('Working...').removeClass('btn-primary').addClass('btn-link');

        });


    });
</script>