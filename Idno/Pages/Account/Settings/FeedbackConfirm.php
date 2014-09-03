<?php

    /**
     * Change user settings
     */

    namespace Idno\Pages\Account\Settings {

        use Idno\Core\Webservice;

        class FeedbackConfirm extends \Idno\Common\Page
        {

            function getContent()
            {
                $this->createGatekeeper(); // Logged-in only please
                $t        = \Idno\Core\site()->template();
                $t->body  = $t->draw('account/settings/feedback/confirm');
                $t->title = 'Thank you for your feedback!';
                $t->drawPage();
            }

        }

    }