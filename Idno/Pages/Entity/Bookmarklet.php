<?php

    /*
     * Idno bookmarklet (forwards to the share screen)
     */

namespace Idno\Pages\Entity {

    /**
     * Idno bookmarklet
     */
    class Bookmarklet extends \Idno\Common\Page
    {

        function getContent()
        {

            $t = \Idno\Core\Idno::site()->template();
            Idno\Core\Idno::site()->response->setContent(($t->draw('entity/bookmarklet')));

        }

    }
}

