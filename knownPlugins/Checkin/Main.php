<?php

    namespace knownPlugins\Checkin {

        class Main extends \known\Common\Plugin {
            function registerPages() {
                \known\Core\site()->addPageHandler('/checkin/edit/?', '\knownPlugins\Checkin\Pages\Edit');
                \known\Core\site()->addPageHandler('/checkin/edit/([A-Za-z0-9]+)/?', '\knownPlugins\Checkin\Pages\Edit');
                \known\Core\site()->addPageHandler('/checkin/delete/([A-Za-z0-9]+)/?', '\knownPlugins\Checkin\Pages\Delete');
                \known\Core\site()->addPageHandler('/checkin/callback/?', '\knownPlugins\Checkin\Pages\Callback');

                \known\Core\site()->template()->extendTemplate('shell/head','checkin/head');
            }
        }

    }