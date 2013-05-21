<?php

    if (!empty($vars['user'])) {
        echo $vars['user']->draw();
    }
    if (!empty($vars['items'])) {

        foreach($vars['items'] as $entry) {
            /* @var \Idno\Entities\ActivityStreamPost $entry */
            echo $entry->draw();
        }

    }