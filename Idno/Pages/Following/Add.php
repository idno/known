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
                    $wc = new Webservice();
                    if ($url = $wc->sanitizeURL($url)) {
                        if ($feed = \Idno\Core\site()->reader()->getFeedURL($url)) {
                            $sub = new Subscription();
                            $sub->setFeedURL($feed);
                            if (!$sub->save()) {
                                \Idno\Core\site()->session()->addMessage("We couldn't save your feed.");
                            }
                        }
                    }
                }
                $this->forward(\Idno\Core\site()->config()->getURL() . 'following/');

            }

        }

    }