<?php

    $url = \Idno\Core\Idno::site()->config()->getDisplayURL() . $vars['search'];
    $title = 'Default content';

    echo $this->__(array( 'url' => $url, 'title' => $title ))->draw('shell/toolbar/content/entry');

?>
