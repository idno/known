<?php

    $href = filter_var($vars['value'], FILTER_SANITIZE_URL);
    if ($vars['label']) $vars['value'] = $vars['label'];

?>
<a href="<?php echo $href; ?>"><?php echo $this->__($vars)->draw('forms/output/text'); ?></a>