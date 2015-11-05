<?php

    /**
     * Post annotations
     */

    namespace Idno\Pages\Annotation {

        use Idno\Common\Entity;

        /**
         * Default class to post annotations
         */
        class Post extends \Idno\Common\Page
        {

            // Handle GET requests to a comment
            function getContent()
            {
                $this->forward($_SERVER['HTTP_REFERER']); // Send the user back to whence they came
            }

            // Handle POST requests: this is where the interesting stuff happens
            function postContent()
            {

                $this->createGatekeeper(); // User is logged in and can post content

                // Get variables
                $body        = $this->getInput('body');
                $object_uuid = $this->getInput('object');
                $type        = $this->getInput('type');
                $user        = \Idno\Core\Idno::site()->session()->currentUser();
                if ($type != 'like') {
                    $type = 'reply';
                }

                if ($object = Entity::getByUUID($object_uuid)) {

                    $has_liked = false;
                    if ($type == 'like') {
                        if ($like_annotations = $object->getAnnotations('like')) {
                            foreach ($like_annotations as $like) {
                                if ($like['owner_url'] == \Idno\Core\Idno::site()->session()->currentUser()->getURL()) {
                                    $object->removeAnnotation($like['permalink']);
                                    $object->save();
                                    $has_liked = true;
                                }
                            }
                        }
                    }

                    if (!$has_liked) {
                        if ($object->addAnnotation($type, $user->getTitle(), $user->getURL(), $user->getIcon(), $body)) {
                            $object->save();
                        }
                    }
                    $this->forward($object->getDisplayURL() . '#comments');
                }

            }

        }

    }