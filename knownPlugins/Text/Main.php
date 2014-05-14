<?php

    namespace knownPlugins\Text {

        class Main extends \known\Common\Plugin {
            function registerPages() {
                \known\Core\site()->addPageHandler('/entry/edit/?', '\knownPlugins\Text\Pages\Edit');
                \known\Core\site()->addPageHandler('/entry/edit/([A-Za-z0-9]+)/?', '\knownPlugins\Text\Pages\Edit');
                \known\Core\site()->addPageHandler('/entry/delete/([A-Za-z0-9]+)/?', '\knownPlugins\Text\Pages\Delete');
                \known\Core\site()->addPageHandler('/entry/([A-Za-z0-9]+)/.*', '\known\Pages\Entity\View');
            }
        }

    }