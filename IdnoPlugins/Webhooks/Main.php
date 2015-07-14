<?php

    namespace IdnoPlugins\Webhooks {

        use Idno\Common\Entity;
        use Idno\Common\Plugin;
        use Idno\Core\Webservice;

        class Main extends Plugin {

            function registerPages() {

                \Idno\Core\site()->addPageHandler('admin/webhooks/?', 'IdnoPlugins\Webhooks\Pages\Admin');

                \Idno\Core\site()->template()->extendTemplate('admin/menu/items', 'webhooks/admin/menu');

            }

            function registerEventHooks()
            {
                \Idno\Core\site()->syndication()->registerService('webhooks', function() {
                    return $this->hasWebhooks();
                }, array('note', 'bookmark', 'event', 'article'));

                if ($this->hasWebhooks()) {
                    if (!empty(\Idno\Core\site()->config()->webhook_syndication)) {
                        foreach(\Idno\Core\site()->config()->webhook_syndication as $hook) {
                            \Idno\Core\site()->syndication()->registerServiceAccount('webhooks', $hook['url'], $hook['title']);
                        }
                    }
                    if (\Idno\Core\site()->session()->isLoggedIn()) {
                        if (!empty(\Idno\Core\site()->session()->currentUser()->webhook_syndication)) {
                            foreach(\Idno\Core\site()->session()->currentUser()->webhook_syndication as $hook) {
                                \Idno\Core\site()->syndication()->registerServiceAccount('webhooks', $hook['url'], $hook['title']);
                            }
                        }
                    }
                }

                $hook_function = function(\Idno\Core\Event $event) {

                    $eventdata = $event->data();
                    if ($this->hasWebhooks()) {
                        $object = $eventdata['object'];
                        if (!empty($object) && $object instanceof Entity && !empty($eventdata['syndication_account'])) {

                            $payload = array();
                            $hook_url = $eventdata['syndication_account'];

                            if ($owner = $object->getOwner()) {
                                $payload['icon_url'] = $owner->getIcon();
                                $payload['username'] = $owner->getHandle();
                            }
                            $payload['content_type'] = $object->getActivityStreamsObjectType();
                            $payload['text'] = $object->getTitle() . ' <' . $object->getURL() . '>';
                            $payload['title'] = $object->getTitle();

                            $client = new Webservice();
                            $client->post($hook_url, json_encode($payload));

                        }
                    }

                };

                \Idno\Core\site()->addEventHook('post/note/webhooks', $hook_function);
                \Idno\Core\site()->addEventHook('post/article/webhooks', $hook_function);
                \Idno\Core\site()->addEventHook('post/bookmark/webhooks', $hook_function);
                \Idno\Core\site()->addEventHook('post/event/webhooks', $hook_function);

            }

            /**
             * Have webhooks been registered in the system?
             * @return bool
             */
            function hasWebhooks()
            {
                if (!empty(\Idno\Core\site()->config()->webhook_syndication) ||
                    (\Idno\Core\site()->session()->isLoggedIn() && !empty(\Idno\Core\site()->session()->currentUser()->webhook_syndication))) {
                    return true;
                }
                return false;
            }

        }

    }