<?php

    /**
     * Account management class
     *
     * @package known
     * @subpackage core
     */

    namespace known\Core {

        class Account extends \known\Common\Component
        {

            function init()
            {

                // Account management
                site()->addPageHandler('/account/settings', '\known\Pages\Account\Settings');
                site()->addPageHandler('/account/settings/homepage', '\known\Pages\Account\Settings\Homepage');

                // Basic registration, if we're allowing it
                if (\known\Core\site()->config()->open_registration == true) {
                    site()->addPageHandler('/account/register', '\known\Pages\Account\Register');
                }

            }

        }

    }

