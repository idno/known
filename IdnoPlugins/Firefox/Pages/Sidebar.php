<?php

    /**
     * Firefox sidebar
     */

    namespace IdnoPlugins\Firefox\Pages {

        /**
         * Default class to serve Firefox-related account settings
         */
        class Sidebar extends \Idno\Common\Page
        {

            function getContent()
            {
                if (!\Idno\Core\Idno::site()->session()->isLoggedIn()) {
                    $this->setResponse(401);
                    $this->forward(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'session/login');
                }

                $t = \Idno\Core\Idno::site()->template();
                $body = $t->draw('firefox/sidebar');
                $t->__(array('title' => 'Sidebar', 'body' => $body, 'hidenav' => true))->drawPage();
            }

        }
    }