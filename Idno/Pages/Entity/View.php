<?php

    /**
     * Generic, backup viewer for entities
     */

    namespace Idno\Pages\Entity {

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
                }
                if (empty($object)) $this->forward(); // TODO: 404

                $this->setPermalink();  // This is a permalink
                $t = \Idno\Core\site()->template();
                $t->__(array(

                    'title' => $object->getTitle(),
                    'body' => $t->__(array('object' => $object->getRelatedFeedItems()))->draw('entity/shell')

                ))->drawPage();
            }

            function webmentionContent() {
                @ini_set('log_errors','On');
                if (!empty($this->arguments[0])) {
                    $object = \Idno\Common\Entity::getByID($this->arguments[0]);
                }
                error_log(var_export($this->arguments,true));
                if (empty($object)) return false;

                return true;
            }

            // Handle POST requests to the entity

            function postContent() {
                if (!empty($this->arguments[0])) {
                    $object = \Idno\Common\Entity::getByID($this->arguments[0]);
                }
                if (empty($object)) $this->forward(); // TODO: 404
                if ($object->saveDataFromInput($this)) {
                    $this->forward($object->getURL());
                }
                $this->forward($_SERVER['HTTP_REFERER']);
            }

            // Handle DELETE requests to the entity

            function deleteContent() {
                if (!empty($this->arguments[0])) {
                    $object = \Idno\Common\Entity::getByID($this->arguments[0]);
                }
                if (empty($object)) $this->forward(); // TODO: 404
                if ($object->delete()) {
                    \Idno\Core\site()->session()->addMessage($object->getTitle() . ' was deleted.');
                }
                $this->forward($_SERVER['HTTP_REFERER']);
            }

        }

    }