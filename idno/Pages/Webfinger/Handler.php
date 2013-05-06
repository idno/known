<?php

/**
 * Webfiiiiinger (da daaaaah dah)
 */

namespace Idno\Pages\Webfinger {

    /**
     * Default class to serve the homepage
     */
    class Handler extends \Idno\Common\Page
    {

        function getContent()
        {

            if (!empty($_GET['resource'])) {
                $acct = $_GET['resource'];
                if (substr($acct, 0, 5) == 'acct:' && strlen($acct) > 8) {
                    $email = substr($acct, 5);
                    if ($user = \Idno\Entities\User::getOne(array('email' => $email))) {
                        site()->events()->dispatch('webfinger', new Event(array('user' => $user)));
                    }
                }
            }

        }

        function postContent()
        {
        }

    }

}