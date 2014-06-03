<?php

    /**
     * PubSubHubbub publishing
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Core {

        class PubSubHubbub extends \Idno\Common\Component
        {

            function init()
            {
            }

            function registerEventHooks()
            {

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
            
            function registerPages()
            {
                // Create an endpoint for subscription pings
                $this->addPageHandler('/pubsub/callback/([A-Za-z0-9]+)/([A-Za-z0-9]+)/?', '\Idno\Pages\Pubsubhubbub\Callback');
                
                // When we follow a user, try and subscribe to their hub
                \Idno\Core\site()->addEventHook('follow', function(\Idno\Core\Event $event) {

		    $user = $event->data()['user'];
                    $following = $event->data()['following'];

                    if (($user instanceof \Idno\Entities\User) && ($following instanceof \Idno\Entities\User)) {
                        
			$url = $following->getURL();
                        
                        if ($hubs = $this->discoverHubs($url)) {
                            
                            \Idno\Core\Webservice::post($hub, [
                                'hub.callback' => \Idno\Core\site()->config->url .'pubsub/callback/' . $user->getID() . '/'.$following->getID(), // Callback, unique to each subscriber
                                'hub.mode' => 'subscribe',
                                'hub.topic'  => $url . '?_t=rss', // Subscribe to rss
                            ]);
                        }
                        
                    }

                });
                
                // Send unfollow notification to their hub
                \Idno\Core\site()->addEventHook('unfollow', function(\Idno\Core\Event $event) {

		    $user = $event->data()['user'];
                    $following = $event->data()['following'];

                    if (($user instanceof \Idno\Entities\User) && ($following instanceof \Idno\Entities\User)) {
                        
			$url = $following->getURL();
                        
                        \Idno\Core\Webservice::post($hub, [
                            'hub.callback' => \Idno\Core\site()->config->url .'pubsub/callback/' . $user->getID() . '/'.$following->getID(), // Callback, unique to each subscriber
                            'hub.mode' => 'unsubscribe',
                            'hub.topic'  => $url . '?_t=rss', // Subscribe to rss
                        ]);
                    }

                });
            }

            /**
             * Find all hub urls.
             */
            private function discoverHubs($url) {
                
                $hubs = [];
                $page = \Idno\Core\Webservice::file_get_contents($url);
                
                if (preg_match_all('/<link href="([^"]+)" rel="hub" ?\/?>/i', $page, $match)) {
                    $hubs = array_merge($match[1]);
                }
                if (preg_match_all('/<link rel="hub" href="([^"]+)" ?\/?>/i', $page, $match)) {
                    $hubs = array_merge($match[1]);
                }
                
                if (count($hubs))
                    return $hubs;
                
                return false;
            }
                        
            /**
             * If this idno installation has a PubSubHubbub hub, send a publish notification to the hub
             * @param string $url
             * @return array
             */
            static function publish($url)
            {
                if ($hub = \Idno\Core\site()->config()->hub) {

                    return \Idno\Core\Webservice::post($hub, [
                        'hub.mode' => 'publish',
                        'hub.url'  => \Idno\Core\site()->config()->feed
                    ]);

                }

                return false;
            }

        }

    }