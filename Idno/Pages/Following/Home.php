<?php

    namespace Idno\Pages\Following {

        use Idno\Common\Page;

        class Home extends Page
        {

            function getContent()
            {

                $this->gatekeeper();
                $subscriptions = \Idno\Core\Idno::site()->reader()->getUserSubscriptions(\Idno\Core\Idno::site()->session()->currentUserUUID());

                \Idno\Core\Idno::site()->template()->__(array(
                    'title' => 'Following',
                    'body'  => \Idno\Core\Idno::site()->template()->__(array('subscriptions' => $subscriptions))->draw('following/home')
                ))->drawPage();

            }

        }

    }