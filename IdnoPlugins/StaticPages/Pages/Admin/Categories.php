<?php

    namespace IdnoPlugins\StaticPages\Pages\Admin {

        use Idno\Common\Page;

        class Categories extends Page {

            function getContent() {
                $this->forward(\Idno\Core\site()->config()->getURL() . 'admin/staticpages/');
            }

            function postContent() {

                $categories = $this->getInput('categories');
                if ($staticpages = \Idno\Core\site()->plugins()->get('StaticPages')) {
                    $staticpages->saveCategories($categories);
                }
                $this->forward(\Idno\Core\site()->config()->getURL() . 'admin/staticpages/');

            }

        }

    }