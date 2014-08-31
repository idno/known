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
                
                $hub_mode = $this->getInput('hub.mode', $this->getInput('hub_mode'));
                $hub_topic = $this->getInput('hub.topic', $this->getInput('hub_topic'));
                $hub_challenge = $this->getInput('hub.challenge', $this->getInput('hub_challenge'));
                $hub_lease_seconds = $this->getInput('hub.lease_seconds', $this->getInput('hub_lease_seconds'));
                
                \Idno\Core\site()->logging->log("Pubsub: $hub_mode verification ping ", LOGLEVEL_DEBUG);
                
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
                            
                            \Idno\Core\site()->logging->log("Pubsub: $hub_challenge", LOGLEVEL_DEBUG);
                            echo $hub_challenge; exit;
                        }
                        break;
                }
                
                $this->deniedContent();
            }

            function post()
            {
                \Idno\Core\site()->logging->log("Pubsub: Ping received", LOGLEVEL_DEBUG);
                
                // Since we've overloaded post, we need to parse the arguments
                $arguments = func_get_args();
                if (!empty($arguments)) $this->arguments = $arguments;
                
                // Find users
                if (!empty($this->arguments[0])) {
                    $subscriber = \Idno\Common\Entity::getByID($this->arguments[0]);
                }
                if (empty($subscriber) || (!($subscriber instanceof \Idno\Entities\User))) {
                    $this->goneContent();
                }
                if (!empty($this->arguments[1])) {
                    $subscription = \Idno\Common\Entity::getByID($this->arguments[1]);
                }
                if (empty($subscription) || (!($subscription instanceof \Idno\Entities\User))) {
                    $this->goneContent();
                }
                
                \Idno\Core\site()->logging->log("Pubsub: Ping received, pinging out...", LOGLEVEL_DEBUG);
                
                \Idno\Core\site()->triggerEvent('pubsubhubbub/ping', [
                    'subscriber' => $subscriber,
                    'subscription' => $subscription,
                    'data' => trim(file_get_contents("php://input"))
                ]);
                
            }

        }

    }