<?php

    /**
     * Administration page: about idno
     */

    namespace Idno\Pages\Admin {

        /**
         * Default class to serve the about page
         */
        class About extends \Idno\Common\Page
        {

            function getContent()
            {
                $this->adminGatekeeper(); // Admins only
                $t        = \Idno\Core\Idno::site()->template();
                $t->body  = $t->draw('admin/about');
                $t->title = \Idno\Core\Idno::site()->language()->_('About Known');
                $t->drawPage();

            }

        }

    }