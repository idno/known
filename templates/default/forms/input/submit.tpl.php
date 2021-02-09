<?php

$vars['type'] = 'submit';
if (empty($vars['class'])) {
    $vars['class'] = "input-submit btn btn-primary";
}
echo $this->__($vars)->draw('forms/input/input');
