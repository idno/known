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
                $t        = \Idno\Core\site()->template();
                $t->body  = $t->draw('account/settings/notifications');
                $t->title = 'Notification settings';
                $t->drawPage();
            }

            function postContent()
            {
                $this->createGatekeeper(); // Logged-in only please
                $user = \Idno\Core\site()->session()->currentUser();

                $notifications = $this->getInput('notifications');

                $user->notifications = $notifications;

                if ($user->save()) {
                    \Idno\Core\site()->session()->addMessage("Your notification preferences were saved.");
                }
                $this->forward($_SERVER['HTTP_REFERER']);
            }

        }

    }