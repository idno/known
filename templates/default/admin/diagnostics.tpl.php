<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <?php echo $this->draw('admin/menu') ?>
        <h1><?php echo \Idno\Core\Idno::site()->language()->_('Diagnostics'); ?></h1>


        <div class="explanation">
            <p>
                <?php echo \Idno\Core\Idno::site()->language()->_('This page provides you with information that may help you or others diagnose any problems you may be experiencing with your Known install.'); ?>
            </p>
        </div>
        
        <div id="diagnostics-basic">
        <?php
            $basics = $vars['basics'];
        if ($basics['status']!='Ok') {
            foreach ($basics['report'] as $item => $details) {

                $class = '';
                if ($details['status']!='Ok') {

                    switch (strtolower($details['status'])) {
                        case 'fail':
                        case 'failure':
                        case 'danger':
                        case 'error' : $class = 'danger';
                            break;
                        case 'warn' :
                        case 'warning': $class = 'warning';
                            break;
                        case 'ok' : $class = 'success';
                            break;
                        default: $class = 'info';
                    }

                    ?>
            <div class="alert alert-<?php echo $class ?>"><?php echo $details['message']; ?></div>
                    <?php
                }

            }
        } else {
            ?>
                
            <div class="alert alert-success"><?php echo \Idno\Core\Idno::site()->language()->_('Basic checks on installation discovered no problems.'); ?></div>
            
                <?php
        }
        ?>
        </div>
    
        <div id="diagnostics-report" style="display: none;">
            <small><pre style="overflow: auto; height: 500px;">
                </pre></small>
        </div>

        <span class="btn btn-primary" id="diagnostics-report-run"><?php echo \Idno\Core\Idno::site()->language()->_('Generate detailed report'); ?></span>

    </div>

</div>

<script>
    $(document).ready(function(){
        $('#diagnostics-report-run').click(function(){
            $(this).html("Generating...").attr('disabled', 'true');
            
            $('#diagnostics-report pre').load('<?php echo \Idno\Core\Idno::site()->currentPage()->currentUrl(); ?>', function(){
                $('#diagnostics-report-run').hide();
                $('#diagnostics-report').fadeIn();
            });
        });
    });
</script>