<?php

/**
 * PubSubHubbub publishing
 *
 * @package idno
 * @subpackage core
 */

namespace Idno\Core {

    class PubSubHubbub extends \Idno\Common\Component {

        function init() {
            
        }

        function registerEventHooks() {

            // Hook into the "saved" event to publish to PuSH when an entity is saved
            \Idno\Core\site()->addEventHook('saved', function (\Idno\Core\Event $event) {
                if ($object = $event->data()['object']) {
                    /* @var \Idno\Common\Entity $object */
                    if ($object->isPublic()) {
                        $url = $object->getURL();
                        \Idno\Core\PubSubHubbub::publish($url);
                    }
                }
            });
        }

        function registerPages() {
            // Create an endpoint for subscription pings
            $this->addPageHandler('/pubsub/callback/([A-Za-z0-9]+)/([A-Za-z0-9]+)/?', '\Idno\Pages\Pubsubhubbub\Callback');

            // When we follow a user, try and subscribe to their hub
            \Idno\Core\site()->addEventHook('follow', function(\Idno\Core\Event $event) {

                $user = $event->data()['user'];
                $following = $event->data()['following'];

                if (($user instanceof \Idno\Entities\User) && ($following instanceof \Idno\Entities\User)) {

                    $url = $following->getURL();

                    // Find self reference from profile url
                    if ($feed = $this->findSelf($url)) {
                        $following->pubsubself = $feed;
                        $following->save();

                        if ($hubs = $this->discoverHubs($url)) {

                            $pending = unserialize($user->pubsub_pending);
                            if (!$pending)
                                $pending = new \stdClass ();
                            if (!is_array($pending->subscribe))
                                $pending->subscribe = [];

                            $pending->subscribe[$following->getID()] = time();
                            $user->pubsub_pending = serialize($pending);
                            $user->save();

                            \Idno\Core\Webservice::post($hub, [
                                'hub.callback' => \Idno\Core\site()->config->url . 'pubsub/callback/' . $user->getID() . '/' . $following->getID(), // Callback, unique to each subscriber
                                'hub.mode' => 'subscribe',
                                'hub.topic' => $feed, // Subscribe to rss
                            ]);
                        }
                    }
                }
            });

            // Send unfollow notification to their hub
            \Idno\Core\site()->addEventHook('unfollow', function(\Idno\Core\Event $event) {

                $user = $event->data()['user'];
                $following = $event->data()['following'];

                if (($user instanceof \Idno\Entities\User) && ($following instanceof \Idno\Entities\User)) {

                    $url = $following->getURL();

                    $pending = unserialize($user->pubsub_pending);
                    if (!$pending)
                        $pending = new \stdClass ();
                    if (!is_array($pending->subscribe))
                        $pending->unsubscribe = [];

                    $pending->unsubscribe[$following->getID()] = time();
                    $user->pubsub_pending = serialize($pending);
                    $user->save();

                    \Idno\Core\Webservice::post($hub, [
                        'hub.callback' => \Idno\Core\site()->config->url . 'pubsub/callback/' . $user->getID() . '/' . $following->getID(), // Callback, unique to each subscriber
                        'hub.mode' => 'unsubscribe',
                        'hub.topic' => $following->pubsubself
                    ]);
                }
            });
        }

        /**
         * Find all hub urls for a given url, by looking at its feeds.
         */
        private function discoverHubs($url) {

            $hubs = [];
            
            // Find the feed
            $feed = $this->findFeed($url);
            
            /*$page = \Idno\Core\Webservice::file_get_contents($url);

            if (preg_match_all('/<link href="([^"]+)" rel="hub" ?\/?>/i', $page, $match)) {
                $hubs = array_merge($match[1]);
            }
            if (preg_match_all('/<link rel="hub" href="([^"]+)" ?\/?>/i', $page, $match)) {
                $hubs = array_merge($match[1]);
            }*/

            if ($feed) {
                
                $page = \Idno\Core\Webservice::file_get_contents($feed);
                
                // We may be looking on a feed
                if (preg_match_all('/<atom:link href="([^"]+)" rel="hub" ?\/?>/i', $page, $match)) {
                    $hubs = array_merge($match[1]);
                }
                if (preg_match_all('/<atom:link rel="hub" href="([^"]+)" ?\/?>/i', $page, $match)) {
                    $hubs = array_merge($match[1]);
                }
            }

            if (count($hubs))
                return $hubs;

            return false;
        }

        /**
         * Find the (first) feed on a given URL.
         * @param type $url
         * @return type
         */
        private function findFeed($url) {
            $feed = null;
            
            $data = \Idno\Core\Webservice::file_get_contents($url);
            // serach for all 'RSS Feed' declarations 
            if (preg_match_all('#<link[^>]+type="application/rss\+xml"[^>]*>#is', $data, $rawMatches)) {
                
                if (preg_match('#href="([^"]+)"#i', $rawMatches[0][0], $rawUrl)) {
                    $feed = $rawUrl[1];
                }

            }
            
            return $feed;
        }
        
        /**
         * Find the self resource.
         * This method will find a link self on a feed, finding the feed first
         * @param type $url
         */
        private function findSelf($url) {

            $self = null;
            $feed = null;

            // Find RSS
            $feed = $this->findFeed($url);

            // Find self
            if ($feed) {
                $data = \Idno\Core\Webservice::file_get_contents($feed);
                
                if (preg_match('/<atom:link[^>]+href="([^"]+)"[^>]*rel="self"[^>]*>/i', $data, $match)) {
                    $self = $match[1];
                }
                if (preg_match('/<atom:link[^>]+rel="self"[^>]*href="([^"]+)"[^>]*>/i', $data, $match)) {
                    $self = $match[1];
                }
            }

            return $self;
        }

        /**
         * If this idno installation has a PubSubHubbub hub, send a publish notification to the hub
         * @param string $url
         * @return array
         */
        static function publish($url) {
            if ($hub = \Idno\Core\site()->config()->hub) {

                return \Idno\Core\Webservice::post($hub, [
                            'hub.mode' => 'publish',
                            'hub.url' => \Idno\Core\site()->config()->feed
                ]);
            }

            return false;
        }

    }

}