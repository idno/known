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
                site()->addPageHandler('/account/settings/notifications/?', '\Idno\Pages\Account\Settings\Notifications');
                site()->addPageHandler('/account/settings/homepage/?', '\Idno\Pages\Account\Settings\Homepage');
                site()->addPageHandler('/account/settings/following/?', '\Idno\Pages\Account\Settings\Following');
                site()->addPageHandler('/account/settings/following/bookmarklet/?', '\Idno\Pages\Account\Settings\Following\Bookmarklet');

                // Basic registration; this is now always present, but the page will reject the user if registration
                // is closed and a valid invitation code hasn't been provided
                site()->addPageHandler('/account/register', '\Idno\Pages\Account\Register', true);

                // Password requests
                site()->addPagehandler('/account/password', '\Idno\Pages\Account\Password', true);
                site()->addPagehandler('/account/password/reset', '\Idno\Pages\Account\Password\Reset', true);

            }

        }

    }

