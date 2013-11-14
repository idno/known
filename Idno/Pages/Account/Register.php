<?php

/**
 * Create a user
 */

    namespace Idno\Pages\Account {

        /**
         * Default class to serve the registration page
         */
        class Register extends \Idno\Common\Page {

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

                $user = new \Idno\Entities\User();

                if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    if (!($emailuser = \Idno\Entities\User::getByEmail($email)) && !($handleuser = \Idno\Entities\User::getByHandle($handle)) &&
                        !empty($handle) && $password == $password2 && strlen($password) > 4 && !empty($name)) {
                        $user = new \Idno\Entities\User();
                        $user->email = $email;
                        $user->handle = $handle;
                        $user->setPassword($password);
                        $user->setTitle($name);
                        if (!\Idno\Entities\User::get()) $user->setAdmin(true);
                        $user->save();
                    } else {
                        if (empty($handle)) {
                            \Idno\Core\site()->session()->addMessage("You can't have an empty handle.");
                        } else if (!empty($handleuser)) {
                            \Idno\Core\site()->session()->addMessage("Unfortunately, a user is already using that handle. Please choose another.");
                        }
                        if (!empty($emailuser)) {
                            \Idno\Core\site()->session()->addMessage("Unfortunately, a user is already using that email address. Please choose another.");
                        }
                        if ($password != $password2 || strlen($password) <= 4) {
                            \Idno\Core\site()->session()->addMessage("Please check that your passwords match and that your password is over four characters long.");
                        }
                    }
                } else {
                    \Idno\Core\site()->session()->addMessage("That doesn't seem to be a valid email address.");
                }

                if (!empty($user->_id)) {
                    \Idno\Core\site()->session()->addMessage("You've registered! Well done. Why not add some profile information?");
                    \Idno\Core\site()->session()->logUserOn($user);
                    $this->forward($user->getURL());
                } else {
                    \Idno\Core\site()->session()->addMessage("We couldn't register you.");
                    $this->forward($_SERVER['HTTP_REFERER']);
                }

            }

        }

    }