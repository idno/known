<?php 

	if (empty($vars['time'])) $vars['time'] = time();

        $tokenid = "tid".md5(mt_rand());
        
        // Normalise tokens
        if (strpos($vars['action'], 'http')!==0) {
            $vars['action'] = \Idno\Core\Idno::site()->config()->getDisplayURL() . trim($vars['action'], ' /');
        }
?>
<div style="display: none;" id="<?= $tokenid; ?>"></div>
<input type="hidden" name="__bTs" value="<?=$vars['time']?>" />
<input type="hidden" name="__bTk" value="<?=\Bonita\Forms::token($vars['action'],$vars['time'])?>" />
<input type="hidden" name="__bTa" value="<?=htmlentities($vars['action'])?>" />
<script>

    setInterval(function() {
        
        var form = $('#<?= $tokenid; ?>').closest('form');
        
        Security.getCSRFToken(function(token, ts) {
           
           form.find('input[name=__bTk]').val(token);
           form.find('input[name=__bTs]').val(ts);
           
        }, form.find('input[name=__bTa]').val());
        
    }, 300000); // update form token every 5 minutes
</script>