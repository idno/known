<?php

    /**
     * Firefox account page
     */

    namespace IdnoPlugins\Firefox\Pages {

        /**
         * Default class to serve Firefox-related account settings
         */
        class Account extends \Idno\Common\Page
        {

            function getContent()
            {
                $this->createGatekeeper(); // Logged-in users only
                $t = \Idno\Core\site()->template();
                $body = $t->draw('account/firefox');
                $t->__(['title' => 'Firefox', 'body' => $body])->drawPage();
            }

        }
    }