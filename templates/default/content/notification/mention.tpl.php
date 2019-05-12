<?php

    echo $t->__([
        'notification' => $notification,
        'interaction' => 'mentioned',
        'icon' => '<i class="fa fa-comment-o"></i>',
        'verb' => ''
    ])->draw('content/notification/wrapper');
