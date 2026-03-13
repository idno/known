<?php

namespace Themes\Twenty26\Pages {

    class Drafts extends \Idno\Common\Page
    {
        function getContent()
        {
            $this->gatekeeper();

            $user = \Idno\Core\Idno::site()->session()->currentUser();
            $count = 25; // Paginate at 25 drafts per page
            $offset = (int) $this->getInput('offset', 0);

            $drafts = \Idno\Common\Entity::get(
                ['publish_status' => 'draft', 'owner' => $user->getUUID()],
                [],
                $count,
                $offset
            );
            $total = \Idno\Common\Entity::count(
                ['publish_status' => 'draft', 'owner' => $user->getUUID()]
            );

            $t = \Idno\Core\Idno::site()->template();
            $t->body = $t->__([
                'drafts' => $drafts,
                'count' => $count,
                'offset' => $offset,
                'total' => $total,
            ])->draw('drafts');
            $t->title = \Idno\Core\Idno::site()->language()->_('Drafts');
            $t->drawPage();
        }
    }

}
