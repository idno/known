<?php

    if (!empty($vars['unique_id'])) {
        $unique_id = $vars['unique_id'];
    } else {
        $unique_id = 'body' . rand(0,9999);
    }
    if (!empty($vars['class'])) {
        $class = $vars['class'];
    } else {
        $class = '';
    }
    if (!empty($vars['height'])) {
        $height = $vars['height'];
    } else {
        $height = 500;
    }
    if (!empty($vars['placeholder'])) {
        $placeholder = $vars['placeholder'];
    } else {
        $placeholder = 'Share something brilliant...';
    }
    if (!empty($vars['value'])) {
        $value = $vars['value'];
    } else {
        $value = '';
    }

?>

<br class="clearall">
<textarea name="<?=$vars['name']?>"  placeholder="<?=htmlspecialchars($placeholder);?>" style="height:<?=$height?>px"
          class="bodyInput mentionable form-control <?=$class?>" id="<?=$unique_id?>"><?= (htmlspecialchars($value)) ?></textarea>

