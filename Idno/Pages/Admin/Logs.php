<?php

namespace Idno\Pages\Admin {

    class Logs extends \Idno\Common\Page {

        function getContent() {
            $this->adminGatekeeper(); // Admins only
            // Retrieved via API, so just dump logs
            if ($this->xhr || \Idno\Core\Idno::site()->session()->isAPIRequest()) {

                echo file_get_contents(\Idno\Core\Idno::site()->config()->getTempDir() . \Idno\Core\Idno::site()->config()->host . '.log');
            } else {

                $t = \Idno\Core\Idno::site()->template();
                $t->body = $t->__([])->draw('admin/logs');
                $t->title = 'Log capture';
                $t->drawPage();
            }
        }

    }

}