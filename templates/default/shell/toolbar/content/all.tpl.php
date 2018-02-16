<?php

    $url = \Idno\Core\Idno::site()->config()->getDisplayURL() . 'content/all/' . $vars['search'];
    $title = \Idno\Core\Idno::site()->language()->_('All content');

    echo $this->__(array( 'url' => $url, 'title' => $title ))->draw('shell/toolbar/content/entry');

?>
