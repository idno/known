<?php

    namespace IdnoPlugins\Styles {

        class Main extends \Idno\Common\Plugin
        {
            function registerPages()
            {
                \Idno\Core\site()->addPageHandler('admin/styles/?', 'IdnoPlugins\Styles\Pages\Admin');
                \Idno\Core\site()->addPageHandler('styles/site/?', 'IdnoPlugins\Styles\Pages\Styles\Site',true);
                \Idno\Core\site()->addPageHandler('settings/styles/?', 'IdnoPlugins\Styles\Pages\Settings');

                \Idno\Core\site()->template()->extendTemplate('admin/menu/items', 'styles/admin/menu');
                \Idno\Core\site()->template()->extendTemplate('settings/menu/items', 'styles/settings/menu');

                if (!empty(\Idno\Core\site()->config()->styles['css'])) {
                    \Idno\Core\site()->template()->extendTemplate('shell/head', 'styles/shell/head');
                }
            }
        }

    }