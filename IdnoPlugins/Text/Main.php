<?php

    namespace IdnoPlugins\Text {

        class Main extends \Idno\Common\Plugin {

            function registerPages() {
                \Idno\Core\Idno::site()->addPageHandler('/entry/edit/?', '\IdnoPlugins\Text\Pages\Edit');
                \Idno\Core\Idno::site()->addPageHandler('/entry/edit/([A-Za-z0-9]+)/?', '\IdnoPlugins\Text\Pages\Edit');
                \Idno\Core\Idno::site()->addPageHandler('/entry/delete/([A-Za-z0-9]+)/?', '\IdnoPlugins\Text\Pages\Delete');
                \Idno\Core\Idno::site()->addPageHandler('/entry/([A-Za-z0-9]+)/.*', '\Idno\Pages\Entity\View');
            }
        }

    }