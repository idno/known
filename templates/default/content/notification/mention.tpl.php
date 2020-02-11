<?php

    echo $t->__([
        'notification' => $notification,
        'interaction' => \Idno\Core\Idno::site()->language()->_('mentioned'),
        'icon' => '<i class="fa fa-comment-o"></i>',
        'verb' => ''
    ])->draw('content/notification/wrapper');
