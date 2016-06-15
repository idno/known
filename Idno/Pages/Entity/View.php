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
                // Check modified ts
                if ($cache = \Idno\Core\Idno::site()->cache()) {
                    if ($modifiedts = $cache->load("{$this->arguments[0]}_modified_ts")) {
                        $this->lastModifiedGatekeeper($modifiedts); // Set 304 and exit if we've not modified this object
                    }
                }

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
                    $this->forward($object->getDisplayURL());
                }

                $this->setOwner($object->getOwner());
                $this->setPermalink(); // This is a permalink

                // We need to set pragma and expires headers
                //header("Pragma: private");
                header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
                header("Cache-Control: post-check=0, pre-check=0", false);
                header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
                header("Pragma: no-cache"); // HTTP/1.0
                header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // Last modified right now

                //header('Expires: ' . date(\DateTime::RFC1123, time() + (86400 * 30))); // Cache for 30 days!
                //$this->setLastModifiedHeader($object->updated); // Say when this was last modified
                if ($cache = \Idno\Core\Idno::site()->cache()) {
                    $cache->store("{$this->arguments[0]}_modified_ts", $object->updated);
                }

                $t = \Idno\Core\Idno::site()->template();
                $t->__(array(

                    'title'       => $object->getTitle(),
                    'body'        => $t->__(['object' => $object])->draw('entity/wrapper'),
                    'description' => $object->getShortDescription()

                ))->drawPage();
            }

            // Get webmention content and handle it

            function webmentionContent($source, $target, $source_response, $source_mf2)
            {
                if (!empty($this->arguments[0])) {
                    $object = \Idno\Common\Entity::getByID($this->arguments[0]);
                    if (empty($object)) {
                        $object = \Idno\Common\Entity::getBySlug($this->arguments[0]);
                    }
                }
                if (empty($object)) {
                    \Idno\Core\Idno::site()->logging->error("No object was found with ID {$this->arguments[0]}.");

                    return false;
                }

                $return = true;

                if ($object instanceof \Idno\Common\Entity && $source != $target && $source != $object->getObjectURL()) {
                    $return = $object->addWebmentions($source, $target, $source_response, $source_mf2);
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
                if ($object->saveDataFromInput()) {
                    $this->forward($object->getDisplayURL());
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
                    \Idno\Core\Idno::site()->session()->addMessage($object->getTitle() . ' was deleted.');
                }
                $this->forward($_SERVER['HTTP_REFERER']);
            }

        }

    }