<?php

/**
 * Change user settings
 */

namespace Idno\Pages\Account {

    /**
     * Default class to serve the homepage
     */
    class Settings extends \Idno\Core\Page
    {

        function getContent()
        {
            $this->gatekeeper(); // Logged-in only please
            $t = \Idno\Core\site()->template();
            $t->body = $t->draw('account/settings');
            $t->title = 'Account settings';
            $t->drawPage();
        }

        function postContent()
        {
            $this->gatekeeper(); // Logged-in only please
            $user = \Idno\Core\site()->session()->currentUser();
            $handle = $this->getInput('handle');
            $email = $this->getInput('email');
            $password = $this->getInput('password');
            $password2 = $this->getInput('password2');

            if (!empty($email) && $email != $user->email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                if (!\Idno\Entities\User::getByEmail($email)) {
                    $user->email = $email;
                }
            }

            if (!empty($password) && $password == $password2) {
                $user->setPassword($password);
            }

            if ($user->save()) {
                \Idno\Core\site()->session()->addMessage("Your details were saved.");
            }
            $this->forward($_SERVER['HTTP_REFERER']);
        }

    }

}