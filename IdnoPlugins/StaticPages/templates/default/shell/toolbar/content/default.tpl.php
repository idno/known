<?php

    $url = \Idno\Core\Idno::site()->config()->getDisplayURL();

    if ($staticpages = \Idno\Core\Idno::site()->plugins()->get('StaticPages')) {
        if (!empty($staticpages->getCurrentHomepageId())) {
            $url .= 'content/default/';
        }
    }

    $url .= $vars['search'];

    $title = 'Default content';

    echo $this->__(array( 'url' => $url, 'title' => $title ))->draw('shell/toolbar/content/entry');

?>
