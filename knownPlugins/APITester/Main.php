<?php

    namespace knownPlugins\APITester {

        class Main extends \known\Common\Plugin {
            function registerPages() {
                \known\Core\site()->addPageHandler('/admin/apitester/?', '\knownPlugins\APITester\Pages\Admin');

                \known\Core\site()->template()->extendTemplate('admin/menu/items', 'apitester/admin/menu');
            }
        }

    }