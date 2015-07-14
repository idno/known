<?php

    namespace Idno\Pages\Following {

        use Idno\Common\Page;

        class Home extends Page
        {

            function getContent()
            {

                $this->gatekeeper();
                $subscriptions = \Idno\Core\site()->reader()->getUserSubscriptions(\Idno\Core\site()->session()->currentUserUUID());

                \Idno\Core\site()->template()->__(array(
                    'title' => 'Following',
                    'body'  => \Idno\Core\site()->template()->__(array('subscriptions' => $subscriptions))->draw('following/home')
                ))->drawPage();

            }

        }

    }