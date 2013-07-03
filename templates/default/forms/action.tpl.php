<?php

	// Default to post
	if (strtolower($vars['method']) != 'get') $vars['method'] = 'post';

	if (strpos($vars['action'], 'http') === false) 
		$vars['action'] = \Idno\Core\site()->config()->url . $vars['action'];
?>
<form method="<?=$vars['method']?>" action="<?=$vars['action']?>" enctype="multipart/form-data">
	
	<?=$vars['body']?>
	<?=$t->draw('forms/token')?>
	
</form>
