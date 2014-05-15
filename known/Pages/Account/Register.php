<?php

    /**
     * Create a user
     */

    namespace known\Pages\Account {

        /**
         * Default class to serve the registration page
         */
        class Register extends \known\Common\Page
        {

            function getContent()
            {
                $this->reverseGatekeeper();
                $t        = \known\Core\site()->template();
                $t->body  = $t->draw('account/register');
                $t->title = 'Register';
                $t->drawPage();
            }

            function postContent()
            {
                $name      = $this->getInput('name');
                $handle    = $this->getInput('handle');
                $password  = $this->getInput('password');
                $password2 = $this->getInput('password2');
                $email     = $this->getInput('email');

                $user = new \known\Entities\User();

                if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    if (!($emailuser = \known\Entities\User::getByEmail($email)) && !($handleuser = \known\Entities\User::getByHandle($handle)) &&
                        !empty($handle) && $password == $password2 && strlen($password) > 4 && !empty($name)
                    ) {
                        $user         = new \known\Entities\User();
                        $user->email  = $email;
                        $user->handle = $handle;
                        $user->setPassword($password);
                        $user->setTitle($name);
                        if (!\known\Entities\User::get()) $user->setAdmin(true);
                        $user->save();
                    } else {
                        if (empty($handle)) {
                            \known\Core\site()->session()->addMessage("You can't have an empty handle.");
                        } else if (!empty($handleuser)) {
                            \known\Core\site()->session()->addMessage("Unfortunately, a user is already using that handle. Please choose another.");
                        }
                        if (!empty($emailuser)) {
                            \known\Core\site()->session()->addMessage("Unfortunately, a user is already using that email address. Please choose another.");
                        }
                        if ($password != $password2 || strlen($password) <= 4) {
                            \known\Core\site()->session()->addMessage("Please check that your passwords match and that your password is over four characters long.");
                        }
                    }
                } else {
                    \known\Core\site()->session()->addMessage("That doesn't seem to be a valid email address.");
                }

                if (!empty($user->_id)) {
                    \known\Core\site()->session()->addMessage("You've registered! You're ready to get started Why not add some profile information?");
                    \known\Core\site()->session()->logUserOn($user);
                    $this->forward($user->getURL());
                } else {
                    \known\Core\site()->session()->addMessage("We couldn't register you.");
                    $this->forward($_SERVER['HTTP_REFERER']);
                }

            }

        }

    }