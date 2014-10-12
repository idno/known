<?php

    namespace Idno\Pages\Following {

        use Idno\Common\Page;
        use Idno\Core\Webmention;
        use Idno\Core\Webservice;
        use Idno\Entities\Reader\Subscription;

        class Add extends Page {

            function getContent() {
                $this->forward(\Idno\Core\site()->config()->getURL() . 'following/');
            }

            function postContent() {

                $this->gatekeeper();
                if ($url = $this->getInput('url')) {
                    if ($feed = \Idno\Core\site()->reader()->getFeedObject($url)) {
                        $this->forward(\Idno\Core\site()->config()->getURL() . 'following/confirm/?feed=' . urlencode($url));
                    } else {
                        \Idno\Core\site()->session()->addMessage("We couldn't find a feed at that site.");
                    }
                }
                $this->forward(\Idno\Core\site()->config()->getURL() . 'following/');

            }

        }

    }