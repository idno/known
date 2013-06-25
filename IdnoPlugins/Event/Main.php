<?php

    namespace IdnoPlugins\Event {

        class Main extends \Idno\Common\Plugin {
            function registerPages() {
                \Idno\Core\site()->addPageHandler('/event/edit/?', '\IdnoPlugins\Event\Pages\Edit');
                \Idno\Core\site()->addPageHandler('/event/edit/([A-Za-z0-9]+)/?', '\IdnoPlugins\Event\Pages\Edit');
                \Idno\Core\site()->addPageHandler('/event/delete/([A-Za-z0-9]+)/?', '\IdnoPlugins\Event\Pages\Delete');
                \Idno\Core\site()->addPageHandler('/event/([A-Za-z0-9]+)/.*', '\Idno\Pages\Entity\View');
            }
        }

    }