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

                $user = \Idno\Core\site()->session()->currentUser();
                if ($messages = \Idno\Core\site()->getVendorMessages()) {
                    \Idno\Core\site()->session()->addMessage($messages);
                }

                $this->forward(\Idno\Core\site()->config()->getDisplayURL());

            }

        }

    }