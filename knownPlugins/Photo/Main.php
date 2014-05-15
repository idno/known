<?php

    namespace knownPlugins\Photo {

        class Main extends \known\Common\Plugin {
            function registerPages() {
                \known\Core\site()->addPageHandler('/photo/edit/?', '\knownPlugins\Photo\Pages\Edit');
                \known\Core\site()->addPageHandler('/photo/edit/([A-Za-z0-9]+)/?', '\knownPlugins\Photo\Pages\Edit');
                \known\Core\site()->addPageHandler('/photo/delete/([A-Za-z0-9]+)/?', '\knownPlugins\Photo\Pages\Delete');
            }
        }

    }