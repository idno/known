<?php

    namespace knownPlugins\Status {

        class Main extends \known\Common\Plugin {
            function registerPages() {
                \known\Core\site()->addPageHandler('/status/edit/?', '\knownPlugins\Status\Pages\Edit');
                \known\Core\site()->addPageHandler('/status/edit/([A-Za-z0-9]+)/?', '\knownPlugins\Status\Pages\Edit');
                \known\Core\site()->addPageHandler('/status/delete/([A-Za-z0-9]+)/?', '\knownPlugins\Status\Pages\Delete');
            }
        }

    }