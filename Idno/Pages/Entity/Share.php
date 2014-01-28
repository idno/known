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

                if (!\Idno\Core\site()->session()->isLoggedIn()) {
                    $this->setResponse(401);
                    $this->forward('/session/login');
                }

                $url = $this->getInput('share_url');
                $title = $this->getInput('share_title');

                $share_type = 'note';

                if ($content = \Idno\Core\Webmention::getPageContent($url)) {
                    if ($mf2 = \Idno\Core\Webmention::parseContent($content['content'])) {
                        if (!empty($mf2['items'])) {
                            foreach($mf2['items'] as $item) {
                                if (!empty($item['type'])) {
                                    if (in_array('h-entry',$item['type'])) {
                                        $share_type = 'reply';
                                    }
                                    if (in_array('h-event',$item['type'])) {
                                        $share_type = 'rsvp';
                                    }
                                }
                            }
                        }
                    }
                }

                $content_type = \Idno\Common\ContentType::getRegisteredForIndieWebPostType($share_type);

                if (!empty($content_type)) {
                    if ($page = \Idno\Core\site()->getPageHandler('/' . $content_type->camelCase($content_type->getEntityClassName()) . '/edit')) {
                        if ($share_type == 'note' && !substr_count($url, 'twitter.com')) {
                            $page->setInput('body', $title . ' ' . $url);
                        } else {
                            $page->setInput('url',$url);
                            if (substr_count($url, 'twitter.com')) {
                                preg_match("|https?://(www\.)?twitter\.com/(#!/)?@?([^/]*)|", $url, $matches);
                                if (!empty($matches[3])) {
                                    $page->setInput('body', '@' . $matches[3] . ' ');
                                }
                            }
                        }
                        $page->setInput('hidenav',true);
                        $page->get();
                    }
                } else {
                    $t = \Idno\Core\site()->template();
                    $body = $t->__(['share_type' => $share_type, 'content_type' => $content_type])->draw('entity/share');
                    $t->__(['title' => 'Share', 'body' => $body, 'hidenav' => true])->drawPage();
                }
            }

        }
    }