<?php

    /**
     * Administration homepage
     */

    namespace Idno\Pages\Admin {

        /**
         * Default class to serve the homepage
         */
        class Management extends \Idno\Common\Page
        {

            function getContent()
            {
                $this->adminGatekeeper(); // Admins only
                $t        = \Idno\Core\site()->template();
                $t->body  = $t->draw('admin/management');
                $t->title = 'User Management';
                $t->drawPage();

            }
            }
            }
?>