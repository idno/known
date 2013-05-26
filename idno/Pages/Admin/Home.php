<?php

    /**
     * Administration homepage
     */

    namespace Idno\Pages\Admin {

        /**
         * Default class to serve the homepage
         */
        class Home extends \Idno\Common\Page
        {

            function getContent()
            {
                $this->adminGatekeeper(); // Admins only
                $t = \Idno\Core\site()->template();
                $t->body = $t->draw('admin/home');
                $t->title = 'Administration';
                $t->drawPage();

            }

        }

    }