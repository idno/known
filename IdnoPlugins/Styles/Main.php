<?php

    namespace IdnoPlugins\Styles {

        class Main extends \Idno\Common\Plugin
        {
            function registerPages()
            {
                \Idno\Core\Idno::site()->addPageHandler('admin/styles/?', 'IdnoPlugins\Styles\Pages\Admin');
                \Idno\Core\Idno::site()->addPageHandler('styles/site/?', 'IdnoPlugins\Styles\Pages\Styles\Site',true);
                //\Idno\Core\Idno::site()->addPageHandler('settings/styles/?', 'IdnoPlugins\Styles\Pages\Settings');

                \Idno\Core\Idno::site()->template()->extendTemplate('admin/menu/items', 'styles/admin/menu');
                //\Idno\Core\Idno::site()->template()->extendTemplate('settings/menu/items', 'styles/settings/menu');

                if (!empty(\Idno\Core\Idno::site()->config()->styles['css'])) {
                    \Idno\Core\Idno::site()->template()->extendTemplate('shell/head/final', 'styles/shell/head');
                }
            }
        }

    }