<?php

    /**
     * Generic, backup viewer for entities
     */

    namespace known\Pages\Entity {

        /**
         * Default class to serve the homepage
         */
        class View extends \known\Common\Page
        {

            // Handle GET requests to the entity

            function getContent()
            {
                if (!empty($this->arguments[0])) {
                    $object = \known\Common\Entity::getByID($this->arguments[0]);
                    if (empty($object)) {
                        $object = \known\Common\Entity::getBySlug($this->arguments[0]);
                    }
                }
                if (empty($object)) {
                    $this->goneContent();
                }

                // From here, we know the object is set
		
		// Check that we can see it
		if (!$object->canRead()) {
		    $this->deniedContent();
		}

                // Just forward to the user's page
                if ($object instanceof \known\Entities\User) {
                    $this->forward($object->getURL());
                }

                $this->setOwner($object->getOwner());
                $this->setPermalink(); // This is a permalink
                $t = \known\Core\site()->template();
                $t->__(array(

                    'title'       => $object->getTitle(),
                    'body'        => $t->__(array('object' => $object->getRelatedFeedItems()))->draw('entity/shell'),
                    'description' => $object->getShortDescription()

                ))->drawPage();
            }

            // Get webmention content and handle it

            function webmentionContent($source, $target, $source_content, $source_mf2)
            {
                if (!empty($this->arguments[0])) {
                    $object = \known\Common\Entity::getByID($this->arguments[0]);
                    if (empty($object)) {
                        $object = \known\Common\Entity::getBySlug($this->arguments[0]);
                    }
                }
                if (empty($object)) return false;

                $return = true;

                if ($object instanceof \known\Common\Entity) {
                    $return = $object->addWebmentions($source, $target, $source_content, $source_mf2);
                }

                return $return;
            }

            // Handle POST requests to the entity

            function postContent()
            {
                if (!empty($this->arguments[0])) {
                    $object = \known\Common\Entity::getByID($this->arguments[0]);
                    if (empty($object)) {
                        $object = \known\Common\Entity::getBySlug($this->arguments[0]);
                    }
                }
                if (empty($object)) $this->forward(); // TODO: 404
                if ($object->saveDataFromInput($this)) {
                    $this->forward($object->getURL());
                }
                $this->forward($_SERVER['HTTP_REFERER']);
            }

            // Handle DELETE requests to the entity

            function deleteContent()
            {
                if (!empty($this->arguments[0])) {
                    $object = \known\Common\Entity::getByID($this->arguments[0]);
                    if (empty($object)) {
                        $object = \known\Common\Entity::getBySlug($this->arguments[0]);
                    }
                }
                if (empty($object)) $this->forward(); // TODO: 404
                if ($object->delete()) {
                    \known\Core\site()->session()->addMessage($object->getTitle() . ' was deleted.');
                }
                $this->forward($_SERVER['HTTP_REFERER']);
            }

        }

    }