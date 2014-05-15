<?php

    /**
     * Delete annotation endpoint.
     */

    namespace known\Pages\Annotation {

        /**
         * Default class to serve the homepage
         */
        class Delete extends \known\Common\Page
        {

            // No point doing get requests for delete functions


            // Handle POST requests 

            function postContent()
            {
                $this->gatekeeper();

                if (!empty($this->arguments[0])) {
                    $object = \known\Common\Entity::getByID($this->arguments[0]);
                    if (empty($object)) {
                        $object = \known\Common\Entity::getBySlug($this->arguments[0]);
                    }
                }
                if (empty($object)) {
                    $this->goneContent();
                }

                $permalink = $object->getUrl() . '/annotations/' . $this->arguments[1];
                if ($object->canEdit()) {
                    if (($object->removeAnnotation($permalink)) && ($object->save())) {
                        \known\Core\site()->session()->addMessage('Annotation ' . $permalink . ' was deleted.');
                    }
                }
                $this->forward($_SERVER['HTTP_REFERER']);
            }

        }

    }