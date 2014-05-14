<?php

    namespace knownPlugins\Like {

        class Main extends \known\Common\Plugin {
            function registerPages() {
                \known\Core\site()->addPageHandler('/like/edit/?', '\knownPlugins\Like\Pages\Edit');
                \known\Core\site()->addPageHandler('/like/edit/([A-Za-z0-9]+)/?', '\knownPlugins\Like\Pages\Edit');
                \known\Core\site()->addPageHandler('/like/delete/([A-Za-z0-9]+)/?', '\knownPlugins\Like\Pages\Delete');
            }
        }

    }