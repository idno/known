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

            // Basic registration
            site()->addPageHandler('/account/register', '\Idno\Pages\Account\Register');

        }

    }

}

