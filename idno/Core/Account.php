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

            // Basic registration, if we're allowing it
            if (\Idno\Core\site()->config()->open_registration == true) {
                site()->addPageHandler('/account/register', '\Idno\Pages\Account\Register');
            }

        }

    }

}

