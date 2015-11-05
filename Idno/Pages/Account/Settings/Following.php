<?php

    /**
     * Change the user's following settings, provides an add user bookmarklet
     */

    namespace Idno\Pages\Account\Settings {

        /**
         * Default class to serve the following settings
         */
        class Following extends \Idno\Common\Page
        {

            function getContent()
            {
                $this->createGatekeeper(); // Logged-in only please
                $t        = \Idno\Core\Idno::site()->template();
                $t->body  = $t->draw('account/settings/following');
                $t->title = 'Following settings';
                $t->drawPage();
            }
        }

    }