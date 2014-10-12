<?php

    namespace Idno\Pages\Following {

        use Idno\Common\Page;

        class Confirm extends Page {

            function getContent() {

                if ($url = $this->getInput('feed')) {

                    if ($feed = \Idno\Core\site()->reader()->getFeedObject($url)) {

                        $items = $feed->retrieveItems();

                        $t = \Idno\Core\site()->template();
                        $t->__(array(
                            'title' => 'Subscribe to ' . $feed->getTitle(),
                            'body' => $t->__(array(
                                'feed' => $feed,
                                'items' => $items
                            ))->draw('following/confirm')
                        ))->drawPage();

                    }

                }

            }

        }

    }