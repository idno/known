<?php

    namespace IdnoPlugins\StaticPages\Pages {

        /**
         * Default class to serve the homepage
         */
        class Homepage extends \Idno\Pages\Homepage
        {

            // Handle GET requests to the entity

            function getContent()
            {
                if ($staticpages = \Idno\Core\Idno::site()->plugins()->get('StaticPages')) {
                    if (!empty($staticpages->getCurrentHomepageId())) {
                        $object = \Idno\Common\Entity::getByID($staticpages->getCurrentHomepageId());
                        if (empty($object)) {
                            $object = \Idno\Common\Entity::getBySlug($staticpages->getCurrentHomepageId());
                        }

                        // If the object doesn't exist or is invalid, unset the homepage and reload
                        if (empty($object) || !($object instanceof \IdnoPlugins\StaticPages\StaticPage) || !$object->canRead()) {
                            $staticpages->clearHomepage();
                            $this->forward('/');
                        }

                        if (!$object->canRead()) $this->goneContent();

                        // Forward if necessary
                        if (!empty($object->forward_url) && !\Idno\Core\Idno::site()->session()->isAdmin()) {
                            $this->forward($object->forward_url);
                        }

                        $this->setOwner($object->getOwner());
                        $this->setPermalink(); // This is a permalink
                        $this->setLastModifiedHeader($object->updated); // Say when this was last modified
                        $t = \Idno\Core\Idno::site()->template();
                        $t->__(array(

                            'title'       => $object->getTitle(),
                            'body'        => $t->__(array('object' => $object))->draw('staticpages/page'),
                            'description' => $object->getShortDescription()

                        ))->drawPage();
                    }

                }

                parent::getContent();
            }

        }

    }
