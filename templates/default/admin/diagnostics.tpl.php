<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <?= $this->draw('admin/menu') ?>
        <h1>Diagnostics</h1>


        <div class="explanation">
            <p>
                This page provides you with information that may help you or others diagnose any problems you may be experiencing with your Known install.
            </p>
        </div>
    </div>



    <div class="col-md-10 col-md-offset-1">

        <div id="diagnostics-report" style="display: none;">
            <small><pre>
                </pre></small>
        </div>

        <span class="btn btn-primary" id="diagnostics-report-run">Generate full report...</span>

    </div>

</div>

<script>
    $(document).ready(function(){
        $('#diagnostics-report-run').click(function(){
            $(this).html("Generating...").attr('disabled', 'true');
            
            $('#diagnostics-report pre').load('<?= \Idno\Core\site()->currentPage()->currentUrl(); ?>', function(){
                $('#diagnostics-report-run').hide();
                $('#diagnostics-report').fadeIn();
            });
        });
    });
</script>