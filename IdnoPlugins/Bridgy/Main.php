<?php

    namespace IdnoPlugins\Bridgy {

        use Idno\Common\Plugin;

        class Main extends Plugin {

            function registerPages() {

                \Idno\Core\site()->template()->extendTemplate('account/menu/items', 'bridgy/menu');

                \Idno\Core\site()->addPageHandler('/account/bridgy/?','IdnoPlugins\Bridgy\Pages\Account');
                \Idno\Core\site()->addPageHandler('/account/bridgy/enabled/?','IdnoPlugins\Bridgy\Pages\Enabled');
                \Idno\Core\site()->addPageHandler('/account/bridgy/disabled/?','IdnoPlugins\Bridgy\Pages\Disabled');
                \Idno\Core\site()->addPageHandler('/account/bridgy/check/?','IdnoPlugins\Bridgy\Pages\Check');

            }

        }

    }