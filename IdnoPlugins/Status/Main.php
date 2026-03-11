<?php

namespace IdnoPlugins\Status {

    class Main extends \Idno\Common\Plugin
    {

        function registerEventHooks()
        {
            parent::registerEventHooks();

            // Handle async link preview fetching
            \Idno\Core\Idno::site()->events()->addListener(
                'linkpreview/fetch', function (\Idno\Core\Event $event) {
                    $eventdata = $event->data();
                    $entityId = $eventdata['entity_id'] ?? '';
                    $url = $eventdata['url'] ?? '';

                    if (empty($entityId) || empty($url)) {
                        return;
                    }

                    $entity = \Idno\Common\Entity::getByID($entityId);
                    if (empty($entity)) {
                        return;
                    }

                    // Check if already cached
                    if (!empty($entity->link_preview) && ($entity->link_preview['url'] ?? '') === $url) {
                        $event->setResponse(['status' => 'already_cached']);
                        return;
                    }

                    // Try to use an existing UnfurledUrl cache first
                    $unfurled = \Idno\Entities\UnfurledUrl::getBySourceURL($url);
                    if (empty($unfurled)) {
                        $unfurled = new \Idno\Entities\UnfurledUrl();
                        $unfurled->setAccess('PUBLIC');
                        $result = $unfurled->unfurl($url);
                        if (!$result) {
                            $event->setResponse(['status' => 'unfurl_failed']);
                            return;
                        }
                        $unfurled->save();
                    }

                    // Extract the preview metadata
                    $data = $unfurled->data;
                    $preview = [
                        'url' => $url,
                        'title' => '',
                        'description' => '',
                        'image' => '',
                        'domain' => parse_url($url, PHP_URL_HOST) ?: '',
                    ];

                    if (!empty($data['og']['og:title'])) {
                        $preview['title'] = $data['og']['og:title'];
                    } elseif (!empty($data['title'])) {
                        $preview['title'] = $data['title'];
                    }

                    if (!empty($data['og']['og:description'])) {
                        $preview['description'] = $data['og']['og:description'];
                    } elseif (!empty($data['description'])) {
                        $preview['description'] = $data['description'];
                    }

                    if (!empty($data['og']['og:image'])) {
                        $preview['image'] = $data['og']['og:image'];
                    }

                    if (!empty($data['og']['og:site_name'])) {
                        $preview['site_name'] = $data['og']['og:site_name'];
                    }

                    // Cache the preview on the entity
                    $entity->link_preview = $preview;
                    $entity->save();

                    $event->setResponse(['status' => 'ok', 'preview' => $preview]);
                }
            );
        }

        function registerPages()
        {
            \Idno\Core\Idno::site()->routes()->addRoute('/status/edit/?', '\IdnoPlugins\Status\Pages\Edit');
            \Idno\Core\Idno::site()->routes()->addRoute('/status/edit/:id/?', '\IdnoPlugins\Status\Pages\Edit');
            \Idno\Core\Idno::site()->routes()->addRoute('/reply/edit/?', '\IdnoPlugins\Status\Pages\Edit');
            \Idno\Core\Idno::site()->routes()->addRoute('/reply/edit/:id/?', '\IdnoPlugins\Status\Pages\Edit');
            \Idno\Core\Idno::site()->routes()->addRoute('/status/delete/:id/?', '\IdnoPlugins\Status\Pages\Delete');
            \Idno\Core\Idno::site()->routes()->addRoute('/reply/delete/:id/?', '\IdnoPlugins\Status\Pages\Delete');
        }

        function registerContentTypes()
        {
            parent::registerContentTypes();

            \Idno\Common\ContentType::register($this->getNamespace() . '\\RepliesContentType');
        }

        function registerTranslations()
        {

            \Idno\Core\Idno::site()->language()->register(
                new \Idno\Core\GetTextTranslation(
                    'status', dirname(__FILE__) . '/languages/'
                )
            );
        }

    }

}
