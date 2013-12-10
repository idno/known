<?php

    namespace IdnoPlugins\APITester {

        class Main extends \Idno\Common\Plugin {
            function registerPages() {
                \Idno\Core\site()->addPageHandler('/admin/apitester/?', '\IdnoPlugins\APITester\Pages\Admin');

                \Idno\Core\site()->template()->extendTemplate('admin/menu/items', 'apitester/admin/menu');
            }
        }

    }