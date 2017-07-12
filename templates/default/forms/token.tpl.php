<?php 

	if (empty($vars['time'])) $vars['time'] = time();

        $tokenid = "tid".md5(mt_rand());
        
?>
<span class="known-security-token" style="display: none;" id="<?= $tokenid; ?>"></span>
<input type="hidden" name="__bTs" value="<?=$vars['time']?>" />
<input type="hidden" name="__bTk" value="<?=\Idno\Core\Bonita\Forms::token($vars['action'],$vars['time'])?>" />
<input type="hidden" name="__bTa" value="<?=htmlentities($vars['action'])?>" />
