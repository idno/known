<?php

    /**
     * PubSubHubbub publishing
     *
     * @package known
     * @subpackage core
     */

    namespace known\Core {

        class PubSubHubbub extends \known\Common\Component
        {

            function init()
            {
            }

            function registerEventHooks()
            {

                // Hook into the "saved" event to publish to PuSH when an entity is saved
                \known\Core\site()->addEventHook('saved', function (\known\Core\Event $event) {
                    if ($object = $event->data()['object']) {
                        /* @var \known\Common\Entity $object */
                        if ($object->isPublic()) {
                            $url = $object->getURL();
                            \known\Core\PubSubHubbub::publish($url);
                        }
                    }
                });

            }

            /**
             * If this known installation has a PubSubHubbub hub, send a publish notification to the hub
             * @param string $url
             * @return array
             */
            static function publish($url)
            {
                if ($hub = \known\Core\site()->config()->hub) {

                    return \known\Core\Webservice::post($hub, [
                        'hub.mode' => 'publish',
                        'hub.url'  => \known\Core\site()->config()->feed
                    ]);

                }

                return false;
            }

        }

    }