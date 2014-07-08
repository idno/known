<?php

    if (empty($vars['user']->icon_number)) {
        $number = rand(1,6);
    } else {
        $number = $vars['user']->number_format;
    }
    echo \Idno\Core\site()->config()->url . 'gfx/users/default-'. str_pad($number, 2, '0', STR_PAD_LEFT) .'.png';

