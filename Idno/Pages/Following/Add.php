<?php

    namespace Idno\Pages\Following {

        use Idno\Common\Page;

        class Add extends Page
        {

            function getContent()
            {
                $this->forward(\Idno\Core\Idno::site()->config()->getURL() . 'following/');
            }

            function postContent()
            {

                $this->gatekeeper();
                if ($url = $this->getInput('url')) {
                    if ($feed = \Idno\Core\Idno::site()->reader()->getFeedObject($url)) {
                        $this->forward(\Idno\Core\Idno::site()->config()->getURL() . 'following/confirm/?feed=' . urlencode($url));
                    } else {
                        \Idno\Core\Idno::site()->session()->addErrorMessage("We couldn't find a feed at that site.");
                    }
                }
                $this->forward(\Idno\Core\Idno::site()->config()->getURL() . 'following/');

            }

        }

    }