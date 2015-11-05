<?php

    namespace IdnoPlugins\Firefox {

        class Main extends \Idno\Common\Plugin {

            function registerPages() {
                // Register settings page
                \Idno\Core\Idno::site()->addPageHandler('account/firefox','\IdnoPlugins\Firefox\Pages\Account');

                // Handlers
                \Idno\Core\Idno::site()->addPageHandler('firefox/share/?','\Idno\Pages\Entity\Share'); // Backwards compatibility
                \Idno\Core\Idno::site()->addPageHandler('firefox/sidebar/?','\IdnoPlugins\Firefox\Pages\Sidebar');

                /** Template extensions */
                // Add Firefox to Tools and Apps screen
                \Idno\Core\Idno::site()->template()->extendTemplate('account/settings/tools/list','account/firefox');
            }

        }

    }