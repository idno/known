<?php

    namespace IdnoPlugins\Bridgy {

        use Idno\Common\Plugin;

        class Main extends Plugin {

            function registerPages() {

                \Idno\Core\Idno::site()->template()->extendTemplate('account/menu/items', 'bridgy/menu');

                \Idno\Core\Idno::site()->addPageHandler('/account/bridgy/?','IdnoPlugins\Bridgy\Pages\Account');
                \Idno\Core\Idno::site()->addPageHandler('/account/bridgy/enabled/?','IdnoPlugins\Bridgy\Pages\Enabled');
                \Idno\Core\Idno::site()->addPageHandler('/account/bridgy/disabled/?','IdnoPlugins\Bridgy\Pages\Disabled');
                \Idno\Core\Idno::site()->addPageHandler('/account/bridgy/check/?','IdnoPlugins\Bridgy\Pages\Check');

            }

        }

    }