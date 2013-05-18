<?php

    if (!empty($vars['contentTypes'])) {

        echo $this->draw('content/create');

    } else {

        echo $this->draw('pages/home/blurb');

    }

    if (!empty($vars['feed'])) {

        foreach($vars['feed'] as $entry) {
            /* @var \Idno\Entities\ActivityStreamPost $entry */
            echo $entry->draw();
        }

    }

?>