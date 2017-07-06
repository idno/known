<?php 

	if (empty($vars['time'])) $vars['time'] = time();

        $tokenid = "tid".md5(mt_rand());
        
?>
<!--This causing all button bars to break out of their containing p-tag with right alignment and become left aligned. Is there a fix?
<div style="display: none;" id="<?= $tokenid; ?>"></div>-->
<input type="hidden" name="__bTs" value="<?=$vars['time']?>" />
<input type="hidden" name="__bTk" value="<?=\Idno\Core\Bonita\Forms::token($vars['action'],$vars['time'])?>" />
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

