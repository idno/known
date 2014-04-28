<?php

    /**
     * Account management class
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Core {

        class Account extends \Idno\Common\Component
        {

            function init()
            {

                // Account management
                site()->addPageHandler('/account/settings', '\Idno\Pages\Account\Settings');
                site()->addPageHandler('/account/settings/homepage', '\Idno\Pages\Account\Settings\Homepage');
		site()->addPageHandler('/account/settings/following', '\Idno\Pages\Account\Settings\Following');

                // Basic registration, if we're allowing it
                if (\Idno\Core\site()->config()->open_registration == true) {
                    site()->addPageHandler('/account/register', '\Idno\Pages\Account\Register');
                }

            }

        }

    }

