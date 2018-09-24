<?php

    $url = \Idno\Core\Idno::site()->config()->getDisplayURL() . $vars['search'];
    $title = \Idno\Core\Idno::site()->language()->_('Default content');

    echo $this->__(array( 'url' => $url, 'title' => $title ))->draw('shell/toolbar/content/entry');

