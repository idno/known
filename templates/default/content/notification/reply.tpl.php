<?php

    echo $t->__(
        [
        'notification' => $notification,
        'interaction' => \Idno\Core\Idno::site()->language()->_('replied to'),
        'icon' => '<i class="fa fa-reply"></i>',
        'verb' => \Idno\Core\Idno::site()->language()->_('Reply')
        ]
    )->draw('content/notification/wrapper');

