<?php

    /**
     * Allow the user to change their notification settings
     */

namespace Idno\Pages\Account\Settings {

    /**
     * Serve the user notifications settings page
     */
    class Notifications extends \Idno\Common\Page
    {

        function getContent()
        {
            $this->createGatekeeper(); // Logged-in only please
            $t        = \Idno\Core\Idno::site()->template();
            $t->body  = $t->draw('account/settings/notifications');
            $t->title = \Idno\Core\Idno::site()->language()->_('Notification settings');
            $t->drawPage();
        }

        function postContent()
        {
            $this->createGatekeeper(); // Logged-in only please
            $user = \Idno\Core\Idno::site()->session()->currentUser();

            $notifications = $this->getInput('notifications');

            // split multi-line string into an array
            if (isset($notifications['ignored_domains'])) {
                $notifications['ignored_domains'] = preg_split('/\s*[\n,]\s*/', $notifications['ignored_domains']);
            }

            $user->notifications = $notifications;

            if ($user->save()) {
                \Idno\Core\Idno::site()->session()->addMessage(\Idno\Core\Idno::site()->language()->_("Your notification preferences were saved."));
            }
            $this->forward(\Idno\Core\Idno::site()->request()->server->get('HTTP_REFERER'));
        }

    }

}

