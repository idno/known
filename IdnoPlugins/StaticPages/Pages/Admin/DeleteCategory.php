<?php

    namespace IdnoPlugins\StaticPages\Pages\Admin {

        use Idno\Common\Page;

        class DeleteCategory extends Page {

            function getContent() {
                $this->forward(\Idno\Core\site()->config()->getURL() . 'admin/staticpages/');
            }

            function postContent() {

                $category = $this->getInput('category');
                if ($staticpages = \Idno\Core\site()->plugins()->get('StaticPages')) {

                    $staticpages->deleteCategory($category);

                }
                $this->forward(\Idno\Core\site()->config()->getURL() . 'admin/staticpages/');

            }

        }

    }