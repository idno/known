<?php

    namespace knownPlugins\Styles {

        class Main extends \known\Common\Plugin
        {
            function registerPages()
            {
                \known\Core\site()->addPageHandler('admin/styles/?', 'knownPlugins\Styles\Pages\Admin');
                \known\Core\site()->addPageHandler('styles/site/?', 'knownPlugins\Styles\Pages\Styles\Site');
                \known\Core\site()->addPageHandler('settings/styles/?', 'knownPlugins\Styles\Pages\Settings');

                \known\Core\site()->template()->extendTemplate('admin/menu/items', 'styles/admin/menu');
                \known\Core\site()->template()->extendTemplate('settings/menu/items', 'styles/settings/menu');

                if (!empty(\known\Core\site()->config()->styles['css'])) {
                    \known\Core\site()->template()->extendTemplate('shell/head', 'styles/shell/head');
                }
            }
        }

    }