<?php

    if (!empty($vars['items'])) {

        foreach($vars['items'] as $entry) {
            echo $this->__(array('object' => $entry->getRelatedFeedItems()))->draw('entity/shell');
        }

        echo $this->drawPagination($vars['count']);

    } else {
	echo $this->draw('pages/home/nocontent');
    }