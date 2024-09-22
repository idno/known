<?php

namespace Idno\Pages\Admin {

    class Logs extends \Idno\Common\Page
    {

        function getContent()
        {
            $this->adminGatekeeper(); // Admins only
            // Retrieved via API, so just dump logs
            if ($this->xhr || \Idno\Core\Idno::site()->session()->isAPIRequest()) {

                \Idno\Core\Idno::site()->response()->setJsonContent(file_get_contents(\Idno\Core\Idno::site()->config()->getTempDir() . \Idno\Core\Idno::site()->config()->host . '.log'));
            } else {

                $t = \Idno\Core\Idno::site()->template();
                $t->body = $t->__([])->draw('admin/logs');
                $t->title = \Idno\Core\Idno::site()->language()->_('Log capture');
                $content = $t->drawPage(false);
                \Idno\Core\Idno::site()->response()->setContent($content);
            }
        }

    }

}
