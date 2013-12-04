<?php

    /**
     * Delete annotation endpoint.
     */

    namespace Idno\Pages\Annotation {

        /**
         * Default class to serve the homepage
         */
        class Delete extends \Idno\Common\Page
        {

            // No point doing get requests for delete functions


            // Handle POST requests 

            function postContent()
            {
                $this->gatekeeper();

                if (!empty($this->arguments[0])) {
                    $object = \Idno\Common\Entity::getByID($this->arguments[0]);
                    if (empty($object)) {
                        $object = \Idno\Common\Entity::getBySlug($this->arguments[0]);
                    }
                }
                if (empty($object)) {
                    $this->goneContent();
                }

                $permalink = $object->getUrl() . '/annotations/' . $this->arguments[1];
                if ($object->canEdit()) {
                    if (($object->removeAnnotation($permalink)) && ($object->save())) {
                        \Idno\Core\site()->session()->addMessage('Annotation ' . $permalink . ' was deleted.');
                    }
                }
                $this->forward($_SERVER['HTTP_REFERER']);
            }

        }

    }