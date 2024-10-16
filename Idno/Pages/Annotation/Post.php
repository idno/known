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
            $this->forward(\Idno\Core\Idno::site()->request()->server->get('HTTP_REFERER')); // Send the user back to whence they came
        }

        // Handle POST requests: this is where the interesting stuff happens
        function postContent()
        {

            $this->createGatekeeper(); // User is logged in and can post content

            if ($this->xhr) {
                \Idno\Core\Idno::site()->template()->setTemplateType('json'); // Set template
            }

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

                if ($this->xhr) {

                    $likes = $object->countAnnotations('like');
                    if ($likes == 1) {
                        $heart_text = 'star';
                    } else {
                        $heart_text = 'stars';
                    }

                    \Idno\Core\Idno::site()->template()->__(
                        [
                        'number' => $likes,
                        'text' => "$likes $heart_text"
                        ]
                    )->drawPage();
                }

                $this->forward($object->getDisplayURL() . '#comments');
            }

            // Missing object, error
            $this->goneContent();
        }

    }

}

