<?php

namespace IdnoPlugins\StaticPages\Pages {

    use Idno\Common\Page;

    class Admin extends Page
    {

        function getContent()
        {

            $this->adminGatekeeper();
            $staticpages = \Idno\Core\Idno::site()->plugins()->get('StaticPages');
            $categories  = $staticpages->getCategories();
            $pages       = $staticpages->getPagesAndCategories();
            $body        = \Idno\Core\Idno::site()->template()->__(['categories' => $categories, 'pages' => $pages])->draw('staticpages/admin');
            \Idno\Core\Idno::site()->template()->__([
                'title' => \Idno\Core\Idno::site()->language()->_('Pages'), 'body' => $body
            ])->drawPage(true, 'settings-shell');

        }

        function postContent()
        {

            $this->adminGatekeeper();

        }

    }

}

