<?php

$vars['type'] = 'button';
if (!$vars['class'])
    $vars['class'] = "input-submit btn";
echo $this->__($vars)->draw('forms/input/input');
