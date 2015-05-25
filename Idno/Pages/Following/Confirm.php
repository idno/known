<?php

    namespace Idno\Pages\Following {

        use Idno\Common\Page;
        use Idno\Entities\Reader\Subscription;

        class Confirm extends Page
        {

            function getContent()
            {

                $this->gatekeeper();

                if ($url = $this->getInput('feed')) {

                    if ($feed = \Idno\Core\site()->reader()->getFeedObject($url)) {

                        $items = $feed->retrieveItems();

                        $t = \Idno\Core\site()->template();
                        $t->__(array(
                            'title' => 'Subscribe to ' . $feed->getTitle(),
                            'body'  => $t->__(array(
                                'feed'  => $feed,
                                'items' => $items
                            ))->draw('following/confirm')
                        ))->drawPage();

                    }

                }

            }

            function postContent()
            {

                $this->gatekeeper();

                if ($url = $this->getInput('feed')) {

                    if ($feed = \Idno\Core\site()->reader()->getFeedObject($url)) {
                        $subscription = new Subscription();
                        $subscription->setOwner(\Idno\Core\site()->session()->currentUser());
                        $subscription->setFeedURL($feed->getFeedURL());
                        $subscription->setTitle(\Idno\Core\site()->session()->currentUser()->getHandle() . ' subscribed to ' . $feed->getTitle());
                        if ($subscription->save()) {
                            \Idno\Core\site()->session()->addMessage("You're following " . $feed->getTitle() . '!');
                            $this->forward(\Idno\Core\site()->config()->getURL() . 'following/');
                        }
                    }
                    $this->forward(\Idno\Core\site()->config()->getURL() . 'following/confirm/?feed=' . urlencode($feed->url));

                }

            }

        }

    }