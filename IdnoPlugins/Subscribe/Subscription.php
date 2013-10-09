<?php

namespace IdnoPlugins\Subscribe {

    class Subscription extends \Idno\Common\Entity {

        function getActivityStreamsObjectType() {
            return 'false';
        }

        function saveDataFromInput() {

            // Create and update subscription object
            $this->subscriber = \Idno\Core\site()->currentPage()->getInput('subscriber');
            $this->subscription = \Idno\Core\site()->currentPage()->getInput('subscribe');


            // Check duplicates
            if ($result = \Idno\Core\site()->db()->getObjects('IdnoPlugins\Subscribe\Subscription', ['subscriber' => $this->subscriber, 'subscription' => $this->subscription]))
                throw new SubscriptionException("{$this->subscriber} already subscribed to updates from {$this->subscription}");

            if ($content = \Idno\Core\Webservice::get($this->subscription)) {

                // For reference, store the domain part so we can quickly see if it's a recognised domain before performing a MF2 parse
                $this->subscription_domain = parse_url($this->subscription, PHP_URL_HOST);

                // Now fetch MF2 of the subscription url
                $this->subscription_mf2 = \Idno\Core\Webmention::parseContent($content['content']);

                // Get the endpoint
                // Get subscriber endpoint
                if (preg_match('~<link href="([^"]+)" rel="http://mapkyc.me/1dM84ud" ?\/?>~', $content['content'], $match)) {
                    $this->subscription_endpoint = $match[1];
                }
                else
                    throw new SubscriptionException('No subscription endpoint found.');
                
                // Attempt to subscribe (makes sure object is not created unless we've got a +ve response from remote server)
                $this->subscribe();
            }
            else
                throw new SubscriptionException("Page {$this->subscription} could not be reached.");

            return $this->save();
        }

        /**
         * Subscribe and get pings
         */
        function subscribe() {

            // Send subscription ping
            if ($result = \Idno\Core\Webservice::post($this->subscription_endpoint, ['subscriber' => $this->subscriber, 'subscribe' => $this->subscription])) {
                if ($result['response'] >= 300) // handle poorly written endpoints, accept any 200 code
                    throw new SubscriptionException("Subscription attempt reported code {$result['response']}");
            }
            
            // Create/maintain following ACL
            $subscr = \Idno\Entities\AccessGroup::getOne([
                'owner' => \Idno\Core\site()->session()->currentUserUUID(),
                'access_group_type' => 'subscription'
            ]);
            
            if (empty($subscr)) {
                $subscr = new \Idno\Entities\AccessGroup();
                $subscr->access_group_type = 'subscription';
            }
            $url = Main::getUserByProfileURL($this->subscription);
            if (!$url) $url = $this->subscription;
            $subscr->addMember($url); // TODO: Internal we're using UUID, but they're actually profiles
            
            
        }

        /**
         * Unsubscribe
         * @throws SubscriptionException
         */
        function unsubscribe() {
            
            // Send subscription ping
            if ($result = \Idno\Core\Webservice::delete($this->subscription_endpoint, ['subscriber' => $this->subscriber, 'subscribe' => $this->subscription])) {
                if ($result['response'] >= 300) // handle poorly written endpoints, accept any 200 code
                    throw new SubscriptionException("Unsubscribe attempt reported code {$result['response']}");
            }
            
            // Create/maintain following ACL
            $subscr = \Idno\Entities\AccessGroup::getOne([
                'owner' => \Idno\Core\site()->session()->currentUserUUID(),
                'access_group_type' => 'subscription'
            ]);
            
            if (empty($subscr)) {
                $subscr = new \Idno\Entities\AccessGroup();
                $subscr->access_group_type = 'subscription';
            }
            $url = Main::getUserByProfileURL($this->subscription);
            if (!$url) $url = $this->subscription;
            $subscr->removeMember($url); // TODO: Internal we're using UUID, but they're actually profiles
        }

    }

}