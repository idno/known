<?php

    /**
     * Generic, backup viewer for entities
     */

    namespace Idno\Pages\Entity {
        use Idno\Common\Entity;

        /**
         * Default class to serve the homepage
         */
        class View extends \Idno\Common\Page
        {

            // Handle GET requests to the entity

            function getContent()
            {
                if (!empty($this->arguments[0])) {
                    $object = \Idno\Common\Entity::getByID($this->arguments[0]);
                    if (empty($object)) {
                        $object = \Idno\Common\Entity::getBySlug($this->arguments[0]);
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
                if ($object instanceof \Idno\Entities\User) {
                    $this->forward($object->getURL());
                }

                $this->setOwner($object->getOwner());
                $this->setPermalink(); // This is a permalink
                $this->setLastModifiedHeader($object->updated); // Say when this was last modified
                $t = \Idno\Core\site()->template();
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
                    $object = \Idno\Common\Entity::getByID($this->arguments[0]);
                    if (empty($object)) {
                        $object = \Idno\Common\Entity::getBySlug($this->arguments[0]);
                    }
                }
                if (empty($object)) {
                    \Idno\Core\site()->logging->log("No object was found with ID {$this->arguments[0]}.", LOGLEVEL_ERROR);
                    return false;
                }

                $return = true;

                if ($object instanceof \Idno\Common\Entity && $source != $target && $source != $object->getObjectURL()) {
                    $return = $object->addWebmentions($source, $target, $source_content, $source_mf2);
                }

                return $return;
            }

            // Handle POST requests to the entity

            function postContent()
            {
                if (!empty($this->arguments[0])) {
                    $object = \Idno\Common\Entity::getByID($this->arguments[0]);
                    if (empty($object)) {
                        $object = \Idno\Common\Entity::getBySlug($this->arguments[0]);
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
                    $object = \Idno\Common\Entity::getByID($this->arguments[0]);
                    if (empty($object)) {
                        $object = \Idno\Common\Entity::getBySlug($this->arguments[0]);
                    }
                }
                if (empty($object)) $this->forward(); // TODO: 404
                if ($object->delete()) {
                    \Idno\Core\site()->session()->addMessage($object->getTitle() . ' was deleted.');
                }
                $this->forward($_SERVER['HTTP_REFERER']);
            }

        }

    }