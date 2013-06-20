<?php

    namespace IdnoPlugins\Checkin {

        class Main extends \Idno\Common\Plugin {
            function registerPages() {
                \Idno\Core\site()->addPageHandler('/checkin/edit/?', '\IdnoPlugins\Checkin\Pages\Edit');
                \Idno\Core\site()->addPageHandler('/checkin/edit/([A-Za-z0-9]+)/?', '\IdnoPlugins\Checkin\Pages\Edit');
                \Idno\Core\site()->addPageHandler('/checkin/delete/([A-Za-z0-9]+)/?', '\IdnoPlugins\Checkin\Pages\Delete');
                \Idno\Core\site()->addPageHandler('/checkin/callback/?', '\IdnoPlugins\Checkin\Pages\Callback');

                \Idno\Core\site()->template()->extendTemplate('shell/head','checkin/head');
            }
        }

    }