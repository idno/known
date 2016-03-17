<?php

    namespace IdnoPlugins\Convoy {

        use Idno\Core\Input;

        class Main extends \Idno\Common\Plugin {

            function registerPages() {
                \Idno\Core\Idno::site()->addPageHandler('withknown/settings/?', '\IdnoPlugins\Convoy\Pages\Settings');
                \Idno\Core\Idno::site()->hijackPageHandler('begin/connect/?', '\IdnoPlugins\Convoy\Pages\Connect');
                \Idno\Core\Idno::site()->addPageHandler('account/settings/services/?', '\IdnoPlugins\Convoy\Pages\Services');
                \Idno\Core\Idno::site()->addPageHandler('withknown/syndication/?', '\IdnoPlugins\Convoy\Pages\Syndication');
                \Idno\Core\Idno::site()->addPageHandler('convoy/token/?', '\IdnoPlugins\Convoy\Pages\Token');

                //if (\Idno\Core\Idno::site()->hub() || \Idno\Core\Idno::site()->session()->isAdmin()) {
                \Idno\Core\Idno::site()->template()->extendTemplate('account/menu/items','convoy/account/menu', true);
                //}

                if ($this->isConvoyEnabled()) {
                    \Idno\Core\Idno::site()->session()->hub_connect = time();
                    \Idno\Core\Idno::site()->known_hub = new \Idno\Core\Hub('https://domains.withknown.com/');
                    \Idno\Core\Idno::site()->hub()->connect();
                    \Idno\Core\Idno::site()->template()->extendTemplate('content/syndication','convoy/syndication');
                    \Idno\Core\Idno::site()->template()->extendTemplate('content/syndication/embed', 'convoy/syndication/embed');
                }
            }

            function registerEventHooks() {
                \Idno\Core\Idno::site()->addEventHook('syndicate', function (\Idno\Core\Event $event) {

                    $object = $event->data()['object'];
                    $object_type = $event->data()['object_type'];
                    $syndication = Input::getInput('syndication');

                    $object_array = $object->saveToArray();
                    $object_array['url'] = $object->getURL();

                    if (\Idno\Core\Idno::site()->hub()) {
                        $results = \Idno\Core\Idno::site()->hub()->makeCall('hub/user/syndication/post',[

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
                if ($token = \Idno\Core\Idno::site()->config()->convoy_token) {
                    return $token;
                }
                return false;
            }

            /**
             * Saves the Convoy token
             * @param $token
             */
            function saveConvoyToken($token) {
                \Idno\Core\Idno::site()->config()->convoy_token = $token;
                \Idno\Core\Idno::site()->config()->save();
            }

            /**
             * Removes the Convoy token
             */
            function removeConvoyToken() {
                \Idno\Core\Idno::site()->config()->convoy_token = false;
                \Idno\Core\Idno::site()->config()->save();
            }

        }

    }