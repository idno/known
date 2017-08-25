<?php

    echo $t->__([
        'notification' => $notification,
        'interaction' => 'liked',
        'icon' => '<i class="fa fa-star"></i>',
        'verb' => '',
        'hide-body' => true
    ])->draw('content/notification/wrapper');

