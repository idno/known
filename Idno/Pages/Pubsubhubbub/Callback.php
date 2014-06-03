<?php

    /**
     * Pubsub endpoint
     */

    namespace Idno\Pages\Pubsubhubbub {

        /**
         * Class to receive pubsub pings
         */
        class Callback extends \Idno\Common\Page
        {

            function getContent()
            {
                if (!empty($this->arguments[0])) {
                    $subscriber = \Idno\Common\Entity::getByID($this->arguments[0]);
                }
                if (empty($subscriber)) {
                    $this->goneContent();
                }
                if (!empty($this->arguments[1])) {
                    $subscription = \Idno\Common\Entity::getByID($this->arguments[1]);
                }
                if (empty($subscription)) {
                    $this->goneContent();
                }
                
                $hub_mode = get_input('hub.mode');
                $hub_topic = get_input('hub.topic');
                $hub_challenge = get_input('hub.challenge');
                $hub_lease_seconds = get_input('hub.lease_seconds');
                
                
                switch ($hub_mode) {
                    case 'subscribe':
                    case 'unsubscribe':
                        $pending = unserialize($subscriber->pubsub_pending);
                        
                        // Check whether the intent is valid
                        if (is_array($pending->$hub_mode) && array_key_exists($subscription->getID(), $pending->$hub_mode)) {
                            unset($pending->$hub_mode[$subscription->getID()]);
                            $subscriber->pubsub_pending = serialize($pending);
                            $subscriber->save();
                            
                            echo $hub_challenge; exit;
                        }
                        break;
                }
                
                $this->deniedContent();
            }

            function postContent()
            {
                // Find users
                if (!empty($this->arguments[0])) {
                    $subscriber = \Idno\Common\Entity::getByID($this->arguments[0]);
                }
                if (empty($subscriber)) {
                    $this->goneContent();
                }
                if (!empty($this->arguments[1])) {
                    $subscription = \Idno\Common\Entity::getByID($this->arguments[1]);
                }
                if (empty($subscription)) {
                    $this->goneContent();
                }
                
                \Idno\Core\site()->triggerEvent('pubsubhubbub/ping', [
                    'subscriber' => $subscriber,
                    'subscription' => $subscription,
                    'data' => trim(file_get_contents("php://input"))
                ]);
                
            }

        }

    }