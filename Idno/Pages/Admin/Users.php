<?php

    /**
     * User administration
     */

    namespace Idno\Pages\Admin {

        class Users extends \Idno\Common\Page
        {

            function getContent()
            {
                $this->adminGatekeeper(); // Admins only
                $t        = \Idno\Core\site()->template();
                $t->body  = $t->draw('admin/users');
                $t->title = 'User Management';
                $t->drawPage();

            }
        }
    }
?>