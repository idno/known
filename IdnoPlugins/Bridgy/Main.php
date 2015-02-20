<?php

    namespace IdnoPlugins\Bridgy {

        use Idno\Common\Plugin;

        class Main extends Plugin {

            function registerPages() {

                \Idno\Core\site()->template()->extendTemplate('account/menu/items', 'bridgy/menu');

                \Idno\Core\site()->addPageHandler('account/bridgy/?','IdnoPlugins\Bridgy\Pages\Account');

            }

        }

    }