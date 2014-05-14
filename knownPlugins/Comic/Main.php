<?php

    namespace knownPlugins\Comic {

        class Main extends \known\Common\Plugin {
            function registerPages() {
                \known\Core\site()->addPageHandler('/comic/edit/?', '\knownPlugins\Comic\Pages\Edit');
                \known\Core\site()->addPageHandler('/comic/edit/([A-Za-z0-9]+)/?', '\knownPlugins\Comic\Pages\Edit');
                \known\Core\site()->addPageHandler('/comic/delete/([A-Za-z0-9]+)/?', '\knownPlugins\Comic\Pages\Delete');
                \known\Core\site()->addPageHandler('/comic/([A-Za-z0-9]+)/.*', '\known\Pages\Entity\View');
            }
        }

    }