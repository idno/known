<?php

    if (!empty($vars['contentTypes'])) {

	if (\Idno\Core\site()->canEdit()) {
	    echo $this->draw('content/create');
	}

    } else {

        echo $this->draw('pages/home/blurb');

    }

    echo $this->draw('entity/feed');

?>