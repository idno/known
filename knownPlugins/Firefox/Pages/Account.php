<?php

    /**
     * Firefox account page
     */

    namespace knownPlugins\Firefox\Pages {

        /**
         * Default class to serve Firefox-related account settings
         */
        class Account extends \known\Common\Page
        {

            function getContent()
            {
                $this->gatekeeper(); // Logged-in users only
                $t = \known\Core\site()->template();
                $body = $t->draw('account/firefox');
                $t->__(['title' => 'Firefox', 'body' => $body])->drawPage();
            }

        }
    }