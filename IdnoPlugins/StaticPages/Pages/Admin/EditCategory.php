<?php

namespace IdnoPlugins\StaticPages\Pages\Admin {

    use Idno\Common\Page;

    class EditCategory extends Page
    {

        function getContent()
        {
            $this->forward(\Idno\Core\Idno::site()->config()->getURL() . 'admin/staticpages/');
        }

        function postContent()
        {

            $category     = $this->getInput('category');
            $new_category = $this->getInput('new_category');
            if ($staticpages = \Idno\Core\Idno::site()->plugins()->get('StaticPages')) {

                $staticpages->editCategory($category, $new_category);

            }
            $this->forward(\Idno\Core\Idno::site()->config()->getURL() . 'admin/staticpages/');

        }

    }

}

