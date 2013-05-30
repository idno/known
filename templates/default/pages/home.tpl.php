<?php

    if (!empty($vars['contentTypes'])) {

        echo $this->draw('content/create');

    } else {

        echo $this->draw('pages/home/blurb');

    }

    if (!empty($vars['items'])) {

        foreach($vars['items'] as $entry) {
            /* @var \Idno\Entities\ActivityStreamPost $entry */
            echo $entry->draw();
        }

        echo $this->drawPagination($vars['count']);

    }

?>