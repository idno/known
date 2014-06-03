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
                
                return $hubs;
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