<?= $this->draw('js/mentions') ?>
<script>
    /* Handle submit button */
    $(document).ready(function() {
        $('div.page-body form').submit(function() {
	        $('input[type=submit]').attr('disabled', 'yes').hide(); //val('Saving...').removeClass('btn-primary').addClass('btn-link');
            //$('input[type=submit]').attr('disabled', 'yes').val('Saving...').removeClass('btn-primary').addClass('btn-link');
            $('.btn-cancel').hide();
            $('#submit-spinner').show();
        });
    });
</script>
<div id="submit-spinner" class="spinner" style="display:none">
  <div class="bounce1"></div>
  <div class="bounce2"></div>
  <div class="bounce3"></div>
</div>