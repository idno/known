<?php

    echo $t->__([
        'notification' => $notification,
        'interaction' => 'shared',
        'icon' => '<i class="fa fa-retweet"></i>',
        'verb' => ''
    ])->draw('content/notification/wrapper');

