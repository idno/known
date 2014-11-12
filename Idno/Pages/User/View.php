<?php

    /**
     * User profile
     */

    namespace Idno\Pages\User {

        /**
         * Default class to serve the homepage
         */
        class View extends \Idno\Common\Page
        {

            // Handle GET requests to the entity

            function getContent()
            {
                if (!empty($this->arguments[0])) {
                    $user = \Idno\Entities\User::getByHandle($this->arguments[0]);
                }
                if (empty($user)) {
                    $this->noContent();
                }

                // Users own their own profiles
                $this->setOwner($user);

                $types          = 'Idno\Entities\ActivityStreamPost';
                $search['verb'] = 'post';

                $offset = (int)$this->getInput('offset');
                $count  = \Idno\Entities\ActivityStreamPost::countFromX($types, array('owner' => $user->getUUID()));
                $feed   = \Idno\Entities\ActivityStreamPost::getFromX($types, array('owner' => $user->getUUID()), array(), \Idno\Core\site()->config()->items_per_page, $offset);

                $last_modified = $user->updated;
                if (!empty($feed) && is_array($feed)) {
                    if ($feed[0]->updated > $last_modified) {
                        $last_modified = $feed[0]->updated;
                    }
                }
                $this->setLastModifiedHeader($last_modified);

                $t = \Idno\Core\site()->template();
                $t->__(array(

                    'title'       => $user->getTitle(),
                    'body'        => $t->__(array('user' => $user, 'items' => $feed, 'count' => $count, 'offset' => $offset))->draw('entity/User/profile'),
                    'description' => 'The ' . \Idno\Core\site()->config()->title . ' profile for ' . $user->getTitle()

                ))->drawPage();
            }

            // Handle POST requests to the entity

            function postContent()
            {
                if (!empty($this->arguments[0])) {
                    $user = \Idno\Entities\User::getByHandle($this->arguments[0]);
                }
                if (empty($user)) $this->forward(); // TODO: 404
                if ($user->saveDataFromInput($this)) {
                    if ($onboarding = $this->getInput('onboarding')) {
                        $services = \Idno\Core\site()->syndication()->getServices();
                        if (!empty($services) || !empty(\Idno\Core\site()->config->force_onboarding_connect)) {
                            $this->forward(\Idno\Core\site()->config()->getURL() . 'begin/connect');
                        } else {
                            $this->forward(\Idno\Core\site()->config()->getURL() . 'begin/publish');
                        }
                    }
                    $this->forward($user->getURL());
                }
                $this->forward($_SERVER['HTTP_REFERER']);
            }

            // Handle DELETE requests to the entity

            function deleteContent()
            {
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