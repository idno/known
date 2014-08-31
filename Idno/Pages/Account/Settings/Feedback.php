<?php

    /**
     * Change user settings
     */

    namespace Idno\Pages\Account\Settings {

        class Feedback extends \Idno\Common\Page
        {

            function getContent()
            {
                $this->createGatekeeper(); // Logged-in only please
                $t        = \Idno\Core\site()->template();
                $t->body  = $t->draw('account/settings/feedback');
                $t->title = 'Send feedback';
                $t->drawPage();
            }

            function postContent()
            {
                $this->createGatekeeper(); // Logged-in only please

                // TODO: send feedback
            }

        }

    }