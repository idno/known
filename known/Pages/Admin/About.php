<?php

    /**
     * Administration page: about known
     */

    namespace known\Pages\Admin {

        /**
         * Default class to serve the about page
         */
        class About extends \known\Common\Page
        {

            function getContent()
            {
                $this->adminGatekeeper(); // Admins only
                $t        = \known\Core\site()->template();
                $t->body  = $t->draw('admin/about');
                $t->title = 'About known';
                $t->drawPage();

            }

        }

    }