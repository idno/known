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
                
                error_log("Pubsub: $hub_mode ping ");
                
                switch ($hub_mode) {
                    case 'subscribe':
                    case 'unsubscribe':
                        $pending = unserialize($subscriber->pubsub_pending);
                        
                        // Check whether the intent is valid
                        if (is_array($pending->$hub_mode) && in_array($subscription->getUUID(), $pending->$hub_mode)) {
                            $new = [];
                            foreach ($pending->$hub_mode as $value)
                                $new[] = $value;
                            
                            $subscriber->pubsub_pending = serialize($new);
                            $subscriber->save();
                            
                            error_log("Pubsub: $hub_challenge");
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
                
                error_log("Pubsub: Ping received, pinging out...");
                
                \Idno\Core\site()->triggerEvent('pubsubhubbub/ping', [
                    'subscriber' => $subscriber,
                    'subscription' => $subscription,
                    'data' => trim(file_get_contents("php://input"))
                ]);
                
            }

        }

    }