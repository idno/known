<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <?= $this->draw('admin/menu') ?>
        <h1>Diagnostics</h1>


        <div class="explanation">
            <p>
                This tool will provide you with a set of diagnostics which may be helpful to you or others to get to the bottom of any problems you may have.
            </p>
            <p>
                Please note, this report may contain sensitive and security related information, so you absolutely must not send it to anyone in an unencrypted form!
            </p>

        </div>
    </div>



    <div class="col-md-10 col-md-offset-1">

        <div id="diagnostics-report" style="display: none;">
            <small><pre>
                </pre></small>
        </div>

        <span class="btn btn-primary" id="diagnostics-report-run">Generate report...</span>

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