<?php

    echo $t->__([
        'notification' => $notification,
        'interaction' => 'RSVPed to',
        'icon' => '<i class="far fa-calendar-check"></i>',
        'verb' => ''
    ])->draw('content/notification/wrapper');

