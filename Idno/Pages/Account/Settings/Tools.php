<?php

    /**
     * Allow the user to change their notification settings
     */

    namespace Idno\Pages\Account\Settings {

        /**
         * Serve the user notifications settings page
         */
        class Tools extends \Idno\Common\Page
        {

            function getContent()
            {
                $this->createGatekeeper(); // Logged-in only please
                $t        = \Idno\Core\site()->template();
                $t->body  = $t->draw('account/settings/tools');
                $t->title = 'Tools and Apps';
                $t->drawPage();
            }

            function postContent()
            {
                
                $this->forward($_SERVER['HTTP_REFERER']);
            }

        }

    }