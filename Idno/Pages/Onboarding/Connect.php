<?php

    /**
     * User profile editing for onboarding
     */

    namespace Idno\Pages\Onboarding {

        class Connect extends \Idno\Common\Page
        {

            function getContent()
            {

                $this->gatekeeper();

                //if ($services = \Idno\Core\site()->syndication()->getServices()) {
                    $user = \Idno\Core\site()->session()->currentUser();

                    $_SESSION['onboarding_passthrough'] = true;

                    $t = \Idno\Core\site()->template();
                    echo $t->__(array(

                        'title'    => "Connect some networks",
                        'body'     => $t->__(array('user' => $user))->draw('onboarding/connect'),
                        'messages' => \Idno\Core\site()->session()->getAndFlushMessages()

                    ))->draw('shell/simple');
                //} else {
                //    $this->forward(\Idno\Core\site()->config()->getURL() . 'begin/publish');
                //}

            }

        }

    }