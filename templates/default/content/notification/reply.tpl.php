<?php

    echo $t->__([
        'notification' => $notification,
        'interaction' => 'replied to',
        'icon' => '<i class="fa fa-reply"></i>',
        'verb' => 'Reply'
    ])->draw('content/notification/wrapper');

