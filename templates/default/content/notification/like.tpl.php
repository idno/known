<?php

    echo $t->__([
        'notification' => $notification,
        'interaction' => 'liked',
        'icon' => '<i class="fa fa-star"></i>',
        'verb' => ''
    ])->draw('content/notification/wrapper');

