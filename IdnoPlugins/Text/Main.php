<?php

    namespace IdnoPlugins\Text {

        class Main extends \Idno\Common\Plugin {
            function registerPages() {
                \Idno\Core\site()->addPageHandler('/entry/edit/?', '\IdnoPlugins\Text\PageEdit');
                \Idno\Core\site()->addPageHandler('/entry/edit/([A-Za-z0-9]+)/?', '\IdnoPlugins\Text\PageEdit');
            }
        }

    }