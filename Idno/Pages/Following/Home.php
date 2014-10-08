<?php

    namespace Idno\Pages\Following {

        use Idno\Common\Page;
        use Idno\Entities\Reader\Subscription;

        class Home extends Page {

            function getContent() {

                $this->gatekeeper();
                $subscriptions = \Idno\Core\site()->reader()->getUserSubscriptions(\Idno\Core\site()->session()->currentUserUUID());

                \Idno\Core\site()->template()->__([
                    'title' => 'Following',
                    'body' => \Idno\Core\site()->template()->__(['subscriptions' => $subscriptions])->draw('following/home')
                ])->drawPage();

            }

        }

    }