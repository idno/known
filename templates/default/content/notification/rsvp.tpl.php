<?php

    echo $t->__([
        'notification' => $notification,
        'interaction' => 'RSVPed to',
        'icon' => '<i class="fa fa-calendar-check-o"></i>',
        'verb' => ''
    ])->draw('content/notification/wrapper');

