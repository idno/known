<?php

    /*
     * Idno share page
     */

    namespace Idno\Pages\Entity {

        /**
         * Idno share screen
         */
        class Share extends \Idno\Common\Page
        {

            function getContent()
            {
                $this->gatekeeper();

                $url   = $this->getInput('share_url', $this->getInput('url'));
                $title = $this->getInput('share_title', $this->getInput('title'));
                $type  = $this->getInput('share_type');

                // Provide a hook to a URL shortener (TODO: Tidy this up when #237 is merged)
                $event = new \Idno\Core\Event();
                $event->setResponse($url);
                \Idno\Core\site()->events()->dispatch('url/shorten', $event);
                $short_url = $event->response();

                if (!in_array($type, array('note','reply','rsvp','like','bookmark'))) {
                    $share_type = 'note';

                    if ($content = \Idno\Core\Webservice::get($url)) {
                        if ($mf2 = \Idno\Core\Webmention::parseContent($content['content'])) {
                            if (!empty($mf2['items'])) {
                                foreach ($mf2['items'] as $item) {
                                    if (!empty($item['type'])) {
                                        if (in_array('h-entry', $item['type'])) {
                                            $share_type = 'reply';
                                        }
                                        if (in_array('h-event', $item['type'])) {
                                            $share_type = 'rsvp';
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $share_type = $type;
                }

                $content_type = \Idno\Common\ContentType::getRegisteredForIndieWebPostType($share_type);
                
                $hide_nav = false;
                if ($this->getInput('via') == 'ff_social') {
                    $hide_nav = true;
                }

                if (!empty($content_type)) {
                    if ($page = \Idno\Core\site()->getPageHandler('/' . $content_type->camelCase($content_type->getEntityClassName()) . '/edit')) {
                        if ($share_type == 'note' /*&& !substr_count($url, 'twitter.com')*/) {
                            $page->setInput('body', $title . ' ' . $short_url);
                        } else {
                            $page->setInput('short-url', $short_url);
                            $page->setInput('url', $url);
                            if (substr_count($url, 'twitter.com')) {
                                preg_match("|https?://([a-z]+\.)?twitter\.com/(#!/)?@?([^/]*)|", $url, $matches);
                                if (!empty($matches[3])) {
                                    $page->setInput('body', '@' . $matches[3] . ' ');
                                }
                            }
                        }
                        $page->setInput('hidenav', $hide_nav);
                        $page->setInput('sharing',true);
                        $page->setInput('share_type', $share_type);
                        $page->get();
                    }
                } else {
                    $t    = \Idno\Core\site()->template();
                    $body = $t->__(array('share_type' => $share_type, 'content_type' => $content_type, 'sharing' => true))->draw('entity/share');
                    $t->__(array('title' => 'Share', 'body' => $body, 'hidenav' => $hide_nav))->drawPage();
                }
            }

        }
    }