<?php

    namespace IdnoPlugins\Video {

        class Main extends \Idno\Common\Plugin {
            function registerPages() {
                \Idno\Core\site()->addPageHandler('/video/edit/?', '\IdnoPlugins\Video\Pages\Edit');
                \Idno\Core\site()->addPageHandler('/video/edit/([A-Za-z0-9]+)/?', '\IdnoPlugins\Video\Pages\Edit');
                \Idno\Core\site()->addPageHandler('/video/delete/([A-Za-z0-9]+)/?', '\IdnoPlugins\Video\Pages\Delete');
            }
        }

    }