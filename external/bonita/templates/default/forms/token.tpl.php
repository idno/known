<?php 

	if (empty($vars['time'])) $vars['time'] = time();

?>
<input type="hidden" name="__bTs" value="<?=$vars['time']?>" />
<input type="hidden" name="__bTk" value="<?=\Bonita\Forms::token($vars['action'],$vars['time'])?>" />
<input type="hidden" name="__bTa" value="<?=htmlentities($vars['action'])?>" />