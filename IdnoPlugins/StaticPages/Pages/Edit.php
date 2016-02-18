<?php

    namespace IdnoPlugins\StaticPages\Pages {

        use Idno\Common\Page;

        class Edit extends Page
        {

            function getContent()
            {

                $this->adminGatekeeper();

                // Are we loading an entity?
                if (!empty($this->arguments)) {
                    $object = \IdnoPlugins\StaticPages\StaticPage::getByID($this->arguments[0]);
                } else {
                    $object = new \IdnoPlugins\StaticPages\StaticPage();
                }

                if ($owner = $object->getOwner()) {
                    $this->setOwner($owner);
                }

                if ($staticpages = \Idno\Core\Idno::site()->plugins()->get('StaticPages')) {

                    $categories = $staticpages->getCategories();
                    if (!empty($object->category)) {
                        $category = $object->category;
                    } else {
                        $category = $this->getInput('category');
                    }

                    $body = \Idno\Core\Idno::site()->template()->__([
                        'categories' => $categories,
                        'category'   => $category,
                        'object'     => $object
                    ])->draw('entity/StaticPage/edit');

                    \Idno\Core\Idno::site()->template()->__([
                        'title' => 'Edit page',
                        'body'  => $body
                    ])->drawPage();

                }

            }

            function postContent()
            {

                $this->adminGatekeeper();

                if (!empty($this->arguments)) {
                    $object = \IdnoPlugins\StaticPages\StaticPage::getByID($this->arguments[0]);
                }
                if (empty($object)) {
                    $object = new \IdnoPlugins\StaticPages\StaticPage();
                }

                if ($object->saveDataFromInput()) {
                    $this->forward($object->getURL());
                } else {
                    $this->forward($_SERVER['HTTP_REFERER']);
                }

            }

        }

    }