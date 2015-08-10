<?php

    namespace IdnoPlugins\Convoy {

        class Main extends \Idno\Common\Plugin {

            function registerPages() {
                \Idno\Core\site()->addPageHandler('withknown/settings/?', '\IdnoPlugins\Convoy\Pages\Settings');
                \Idno\Core\site()->hijackPageHandler('begin/connect/?', '\IdnoPlugins\Convoy\Pages\Connect');
                \Idno\Core\site()->addPageHandler('account/settings/services/?', '\IdnoPlugins\Convoy\Pages\Services');
                \Idno\Core\site()->addPageHandler('withknown/syndication/?', '\IdnoPlugins\Convoy\Pages\Syndication');
                \Idno\Core\site()->addPageHandler('convoy/token/?', '\IdnoPlugins\Convoy\Pages\Token');

                //if (\Idno\Core\site()->hub() || \Idno\Core\site()->session()->isAdmin()) {
                \Idno\Core\site()->template()->extendTemplate('account/menu/items','convoy/account/menu', true);
                //}

                if ($this->isConvoyEnabled()) {
                    \Idno\Core\site()->session()->hub_connect = time();
                    \Idno\Core\site()->known_hub = new \Idno\Core\Hub('https://domains.withknown.com/');
                    \Idno\Core\site()->hub()->connect();
                    \Idno\Core\site()->template()->extendTemplate('content/syndication','convoy/syndication');
                    \Idno\Core\site()->template()->extendTemplate('content/syndication/embed', 'convoy/syndication/embed');
                }
            }

            function registerEventHooks() {
                \Idno\Core\site()->addEventHook('syndicate', function (\Idno\Core\Event $event) {

                    $object = $event->data()['object'];
                    $object_type = $event->data()['object_type'];
                    $syndication = \Idno\Core\site()->currentPage()->getInput('syndication');

                    $object_array = $object->saveToArray();
                    $object_array['url'] = $object->getURL();

                    if (\Idno\Core\site()->hub()) {
                        $results = \Idno\Core\site()->hub()->makeCall('hub/user/syndication/post',[

                            'object' => $object_array,
                            'object_type' => $object_type,
                            'syndication' => $syndication

                        ]);

                        if ($object_array = json_decode($results['content'],true)) {
                            $object->loadFromArray($object_array);
                            $object->save();
                        }
                    }

                });
            }

            /**
             * Is Convoy enabled for this site?
             * @return bool False for now ...
             */
            function isConvoyEnabled() {
                if ($this->getConvoyToken()) {
                    return true;
                }
                return false;
            }

            /**
             * Get Convoy token
             * @return bool
             */
            function getConvoyToken() {
                if ($token = \Idno\Core\site()->config()->convoy_token) {
                    return $token;
                }
                return false;
            }

            /**
             * Saves the Convoy token
             * @param $token
             */
            function saveConvoyToken($token) {
                \Idno\Core\site()->config()->convoy_token = $token;
                \Idno\Core\site()->config()->save();
            }

            /**
             * Removes the Convoy token
             */
            function removeConvoyToken() {
                \Idno\Core\site()->config()->convoy_token = false;
                \Idno\Core\site()->config()->save();
            }

        }

    }