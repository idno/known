<?php

    /**
     * Generic shortlink forwarder for entities
     */

    namespace Idno\Pages\Entity {

        /**
         * Default class to serve the homepage
         */
        class Shortlink extends \Idno\Common\Page
        {

            // Handle GET requests to the entity

            function getContent()
            {
                if (!empty($this->arguments[0])) {
                    $object = \Idno\Common\Entity::getByShortURL($this->arguments[0]);
                }
                if (empty($object)) {
                    $this->goneContent();
                }
                header("HTTP/1.1 301 Moved Permanently");
                $this->forward($object->getDisplayURL());
            }

            // Get webmention content and handle it

            function webmentionContent($source, $target, $source_content, $source_mf2)
            {
                if (!empty($this->arguments[0])) {
                    $object = \Idno\Common\Entity::getByShortURL($this->arguments[0]);
                }
                if (empty($object)) return false;

                $return = true;

                if ($object instanceof \Idno\Common\Entity) {
                    $return = $object->addWebmentions($source, $target, $source_content, $source_mf2);
                }

                return $return;
            }

        }

    }
    