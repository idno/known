<?php

    /**
     * Handle default annotation
     */

    namespace known\Pages\Annotation {

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

                $this->setOwner($object->getOwner());

                $permalink  = $object->getUrl() . '/annotations/' . $this->arguments[1];
                $annotation = $object->getAnnotation($permalink);
                $subtype    = $object->getAnnotationSubtype($permalink);

                $this->setPermalink(); // This is a permalink
                $t = \known\Core\site()->template();
                $t->__(array(

                    'title'       => $object->getTitle(),
                    'body'        => $t->__(array('annotation' => $annotation, 'subtype' => $subtype, 'permalink' => $permalink, 'object' => $object))->draw('entity/annotations/shell'),
                    'description' => $object->getShortDescription()

                ))->drawPage();
            }

        }

    }