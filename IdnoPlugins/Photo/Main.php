<?php

    namespace IdnoPlugins\Photo {

        class Main extends \Idno\Common\Plugin {
            function registerPages() {
                \Idno\Core\site()->addPageHandler('/photo/edit/?', '\IdnoPlugins\Photo\Pages\Edit');
                \Idno\Core\site()->addPageHandler('/photo/edit/([A-Za-z0-9]+)/?', '\IdnoPlugins\Photo\Pages\Edit');
                \Idno\Core\site()->addPageHandler('/photo/delete/([A-Za-z0-9]+)/?', '\IdnoPlugins\Photo\Pages\Delete');
            }
        }

    }