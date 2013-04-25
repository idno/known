<?php

/**
 * Create a user
 */

    namespace Idno\Pages\Account {

        /**
         * Default class to serve the registration page
         */
        class Register extends \Idno\Core\Page {

            function getContent() {
                $this->reverseGatekeeper();
                $t = \Idno\Core\site()->template();
                $t->body = $t->draw('account/register');
                $t->title = 'Register';
                $t->drawPage();
            }

            function postContent()
            {
                $name = $this->getInput('name');
                $handle = $this->getInput('handle');
                $password = $this->getInput('password');
                $password2 = $this->getInput('password2');
                $email = $this->getInput('email');

                if (!empty($email) && $email != $user->email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    if (!\Idno\Entities\User::getByEmail($email) && !\Idno\Entities\User::getByHandle($handle) &&
                        !empty($handle) && $password == $password2 && strlen($password) > 4 && !empty($name)) {
                        $user = new \Idno\Entities\User();
                        $user->email = $email;
                        $user->handle = $handle;
                        $user->setPassword($password);
                        $user->setTitle($name);
                    }
                }

                if ($user->save()) {
                    \Idno\Core\site()->session()->addMessage("You've registered! Well done.");
                    \Idno\Core\site()->session()->logUserOn($user);
                }

            }

        }

    }