<?php

    // Generate a unique ID for this form and link
	$uniqueID = uniqid('f');

    // Get HTTP method (GET, POST, PUT and DELETE supported for now)
    if (empty($vars['method']) || !in_array($vars['method'],array('GET','POST','PUT','DELETE'))) $vars['method'] = 'POST';

?>
<a <?php if (!empty($vars['class'])) { ?> class="<?=$vars['class'];?>" <?php } ?> href="<?=($vars['url'])?>" onclick="$('#<?=$uniqueID?>').submit(); return false;"><?=($vars['label'])?></a>

<?php

    ob_start();

?>
<form action="<?=($vars['url'])?>" style="display: none; margin: 0; padding: 0" id="<?=$uniqueID?>" method="<?=$vars['method']?>">
    <textarea name="json"><?=htmlspecialchars(json_encode($vars['data']))?></textarea>
    <?=  \Idno\Core\site()->actions()->signForm($vars['url']);?>
</form>
<?php

    $form = ob_get_clean();
    \Idno\Core\site()->template()->extendTemplateWithContent('shell/footer', $form);

?>