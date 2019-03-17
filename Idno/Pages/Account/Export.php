<?php

namespace Idno\Pages\Account {

    use Idno\Common\Page;
    use Idno\Core\Idno;

    class Export extends Page
    {

        function getContent()
        {

            $this->gatekeeper();

            $t = Idno::site()->template();

            $t->__(
                [
                    'title' => \Idno\Core\Idno::site()->language()->_('Export Data'),
                    'body'  => $t->draw('account/export')
                ]
            )->drawPage();

        }

    }

}

