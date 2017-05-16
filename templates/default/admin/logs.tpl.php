<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <?= $this->draw('admin/menu') ?>
        <h1>Log capture</h1>


        <div class="explanation">
            <p>
                This page provides you with captured PHP logs. These contain sensitive information, so be very careful how you disclose the information and to whom.
            </p>
        </div>
        
    
        <div id="logs-report" style="display: none; ">
            <small><pre style="height: 600px; overflow:auto;">
                </pre></small>
        </div>

        <span class="btn btn-primary" id="logs-report-run">Refresh...</span>

    </div>

</div>

<script>
    $(document).ready(function(){
        $('#logs-report pre').load('<?= \Idno\Core\Idno::site()->currentPage()->currentUrl(); ?>', function(){
                $('#logs-report').fadeIn();
            });
            
        $('#logs-report-run').click(function(){
            
            $('#logs-report pre').load('<?= \Idno\Core\Idno::site()->currentPage()->currentUrl(); ?>', function(){
            });
        });
    });
</script>