<?php

    if (!empty($vars['contentTypes'])) {

	if (\Idno\Core\site()->canEdit()) {
	    echo $this->draw('content/create');
	}

    } else {

        echo $this->draw('pages/home/blurb');

    }

    if (!empty($vars['items'])) {

        foreach($vars['items'] as $entry) {
            echo $this->__(array('object' => $entry->getRelatedFeedItems()))->draw('entity/shell');
        }

        echo $this->drawPagination($vars['count']);

    }

?>