<?php

    /**
     * Firefox sidebar
     */

    namespace knownPlugins\Firefox\Pages {

        /**
         * Default class to serve Firefox-related account settings
         */
        class Sidebar extends \known\Common\Page
        {

            function getContent()
            {
                if (!\known\Core\site()->session()->isLoggedIn()) {
                    $this->setResponse(401);
                    $this->forward('/session/login');
                }

                $t = \known\Core\site()->template();
                $body = $t->draw('firefox/sidebar');
                $t->__(['title' => 'Sidebar', 'body' => $body, 'hidenav' => true])->drawPage();
            }

        }
    }