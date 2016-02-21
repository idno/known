<?php

    $url = \Idno\Core\Idno::site()->config()->getDisplayURL() . 'content/all/' . $vars['search'];
    $title = 'All content';

    echo $this->__(array( 'url' => $url, 'title' => $title ))->draw('shell/toolbar/content/entry');

?>
