<?php

    if (!empty($vars['user'])) {
        echo $vars['user']->draw();
    }
    if (!empty($vars['feed'])) {

        foreach($vars['feed'] as $entry) {
            /* @var \Idno\Entities\ActivityStreamPost $entry */
            echo $entry->draw();
        }

    }