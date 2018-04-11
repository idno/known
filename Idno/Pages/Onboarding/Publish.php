<?php

    /**
     * User profile editing for onboarding
     */

    namespace Idno\Pages\Onboarding {

        class Publish extends \Idno\Common\Page
        {

            function getContent()
            {

                $this->gatekeeper();

                $user = \Idno\Core\Idno::site()->session()->currentUser();
                if ($messages = \Idno\Core\Vendor::getMessages()) {
                    \Idno\Core\Idno::site()->session()->addMessage($messages);
                }

                $this->forward(\Idno\Core\Idno::site()->config()->getDisplayURL());

            }

        }

    }