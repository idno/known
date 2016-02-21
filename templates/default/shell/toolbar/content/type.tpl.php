<?php

    $content_type = $vars['content_type'];

    if (!empty($content_type)) {

        $url = \Idno\Core\Idno::site()->config()->getDisplayURL() . 'content/' . $content_type->getCategoryTitleSlug() . '/' . $vars['search'];
        $icon = $content_type->getIcon();
        $title = $content_type->getCategoryTitle();

        echo $this->__(array( 'url' => $url, 'icon' => $icon, 'title' => $title ))->draw('shell/toolbar/content/entry');

    }

?>
