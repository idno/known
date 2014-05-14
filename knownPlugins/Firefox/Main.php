<?php

    namespace knownPlugins\Firefox {

        class Main extends \known\Common\Plugin {

            function registerPages() {
                // Register settings page
                \known\Core\site()->addPageHandler('account/firefox','\knownPlugins\Firefox\Pages\Account');

                // Handlers
                \known\Core\site()->addPageHandler('firefox/share/?','\known\Pages\Entity\Share'); // Backwards compatibility
                \known\Core\site()->addPageHandler('firefox/sidebar/?','\knownPlugins\Firefox\Pages\Sidebar');

                /** Template extensions */
                // Add menu items to account screen
                \known\Core\site()->template()->extendTemplate('account/menu/items','account/firefox/menu');
            }

        }

    }