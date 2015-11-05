<?php

    $bn = hexdec(substr(md5($vars['user']->uuid), 0, 15));
    $number = 1 + ($bn % 5);
    echo \Idno\Core\Idno::site()->config()->getDisplayURL() . 'gfx/users/default-'. str_pad(abs($number), 2, '0', STR_PAD_LEFT) .'.png';

