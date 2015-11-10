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
                Idno::site()->addPageHandler('/account/settings/?', '\Idno\Pages\Account\Settings');
                Idno::site()->addPageHandler('/account/settings/notifications/?', '\Idno\Pages\Account\Settings\Notifications');
                Idno::site()->addPageHandler('/account/settings/tools/?', '\Idno\Pages\Account\Settings\Tools');
                Idno::site()->addPageHandler('/account/settings/following/?', '\Idno\Pages\Account\Settings\Following');
                Idno::site()->addPageHandler('/account/settings/following/bookmarklet/?', '\Idno\Pages\Account\Settings\Following\Bookmarklet');

                // Basic registration; this is now always present, but the page will reject the user if registration
                // is closed and a valid invitation code hasn't been provided
                Idno::site()->addPageHandler('/account/register/?', '\Idno\Pages\Account\Register', true);

                // Password requests
                Idno::site()->addPagehandler('/account/password/?', '\Idno\Pages\Account\Password', true);
                Idno::site()->addPagehandler('/account/password/reset/?', '\Idno\Pages\Account\Password\Reset', true);

                // Known feedback
                Idno::site()->addPageHandler('/account/settings/feedback/?', '\Idno\Pages\Account\Settings\Feedback');
                Idno::site()->addPageHandler('/account/settings/feedback/confirm/?', '\Idno\Pages\Account\Settings\FeedbackConfirm');

            }

        }

    }

