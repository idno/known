<?php

    namespace IdnoPlugins\Text {

        class Main extends \Idno\Common\Plugin {
            function registerPages() {
                \Idno\Core\site()->addPageHandler('/text/edit/?', '\IdnoPlugins\Text\PageEdit');
                \Idno\Core\site()->addPageHandler('/text/edit/:string/?', '\IdnoPlugins\Text\PageEdit');
            }
        }

    }