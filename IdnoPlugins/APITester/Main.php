<?php

    namespace IdnoPlugins\APITester {

        class Main extends \Idno\Common\Plugin {

            function registerPages() {
                \Idno\Core\Idno::site()->addPageHandler('/admin/apitester/?', '\IdnoPlugins\APITester\Pages\Admin');

                \Idno\Core\Idno::site()->template()->extendTemplate('admin/menu/items', 'apitester/admin/menu');
            }

        }

    }