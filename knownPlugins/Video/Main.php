<?php

    namespace knownPlugins\Video {

        class Main extends \known\Common\Plugin {
            function registerPages() {
                \known\Core\site()->addPageHandler('/video/edit/?', '\knownPlugins\Video\Pages\Edit');
                \known\Core\site()->addPageHandler('/video/edit/([A-Za-z0-9]+)/?', '\knownPlugins\Video\Pages\Edit');
                \known\Core\site()->addPageHandler('/video/delete/([A-Za-z0-9]+)/?', '\knownPlugins\Video\Pages\Delete');
            }
        }

    }