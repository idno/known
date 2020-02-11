<?php

    echo $t->__([
        'notification' => $notification,
        'interaction' => \Idno\Core\Idno::site()->language()->_('liked'),
        'icon' => '<i class="fa fa-star"></i>',
        'verb' => '',
        'hide-body' => true
    ])->draw('content/notification/wrapper');

