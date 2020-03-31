<?php

    echo $t->__([
        'notification' => $notification,
        'interaction' => \Idno\Core\Idno::site()->language()->_('shared'),
        'icon' => '<i class="fa fa-retweet"></i>',
        'verb' => ''
    ])->draw('content/notification/wrapper');

