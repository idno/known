<?php

	// Default to post
	if (strtolower($vars['method']) != 'get') $vars['method'] = 'post';

?>
<form method="<?=$vars['method']?>" action="<?=$vars['action']?>" enctype="multipart/form-data">
	
	<?=$vars['body']?>
	<?=$t->draw('forms/token')?>
	
</form>