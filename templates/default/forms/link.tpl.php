<?php

    // Generate a unique ID for this form and link
	$uniqueID = uniqid('f');

?>
<a href="<?=($vars['url'])?>" onclick="$('#<?=$uniqueID?>').submit(); return false;"><?=htmlspecialchars($vars['label'])?></a>
<form action="<?=($vars['url'])?>" style="display: none" id="<?=$uniqueID?>" method="post">
    <textarea name="json"><?=htmlspecialchars(json_encode($vars['data']))?></textarea>
</form>