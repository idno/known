<?php

    echo $t->__([
        'notification' => $notification,
        'interaction' => 'mentioned',
        'icon' => '<i class="far fa-comment"></i>',
        'verb' => ''
    ])->draw('content/notification/wrapper');
