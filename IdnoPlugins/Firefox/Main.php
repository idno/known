<?php

    namespace IdnoPlugins\Firefox {

        class Main extends \Idno\Common\Plugin {

            function registerPages() {
                // Register settings page
                \Idno\Core\site()->addPageHandler('account/firefox','\IdnoPlugins\Firefox\Pages\Account');

                // Handlers
                \Idno\Core\site()->addPageHandler('firefox/share/?','\Idno\Pages\Entity\Share'); // Backwards compatibility
                \Idno\Core\site()->addPageHandler('firefox/sidebar/?','\IdnoPlugins\Firefox\Pages\Sidebar');

                /** Template extensions */
                // Add menu items to account screen
                \Idno\Core\site()->template()->extendTemplate('account/menu/items','account/firefox/menu');
            }

        }

    }