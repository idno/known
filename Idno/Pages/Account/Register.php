<?php

    /**
     * Create a user
     */

    namespace Idno\Pages\Account {

        /**
         * Default class to serve the registration page
         */
        class Register extends \Idno\Common\Page
        {

            function getContent()
            {
                $this->reverseGatekeeper();
                $code       = $this->getInput('code');
                $email      = $this->getInput('email');
                $onboarding = $this->getInput('onboarding');

                if (empty(\Idno\Core\site()->config()->open_registration)) {
                    if (!\Idno\Entities\Invitation::validate($email, $code)) {
                        \Idno\Core\site()->session()->addMessage("Your invitation doesn't seem to be valid, or has expired.");
                        $this->forward(\Idno\Core\site()->config()->getURL());
                    }
                }

                $t = \Idno\Core\site()->template();
                if (empty($onboarding)) {
                    $t->body  = $t->__(['email' => $email, 'code' => $code])->draw('account/register');
                    $t->title = 'Create a new account';
                    $t->drawPage();
                } else {
                    $t->body  = $t->__(['email' => $email, 'code' => $code])->draw('onboarding/register');
                    $t->title = 'Create a new account';
                    echo $t->draw('shell/simple');
                }
            }

            function postContent()
            {
                $name       = $this->getInput('name');
                $handle     = $this->getInput('handle');
                $password   = $this->getInput('password');
                $password2  = $this->getInput('password2');
                $email      = $this->getInput('email');
                $code       = $this->getInput('code');
                $onboarding = $this->getInput('onboarding');

                if (empty(\Idno\Core\site()->config()->open_registration)) {
                    if (!($invitation = \Idno\Entities\Invitation::validate($email, $code))) {
                        \Idno\Core\site()->session()->addMessage("Your invitation doesn't seem to be valid or has expired.");
                        $this->forward(\Idno\Core\site()->config()->getURL());
                    } else {
                        $invitation->delete(); // Remove the invitation; it's no longer needed
                    }
                }

                $user = new \Idno\Entities\User();

                if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    if (!($emailuser = \Idno\Entities\User::getByEmail($email)) && !($handleuser = \Idno\Entities\User::getByHandle($handle))
                        && !empty($handle) && strlen($handle <= 32) && !substr_count($handle, '/') && $password == $password2 && strlen($password) > 4
                    ) {
                        $user         = new \Idno\Entities\User();
                        $user->email  = $email;
                        $user->handle = strtolower(trim($handle)); // Trim the handle and set it to lowercase
                        $user->setPassword($password);
                        if (empty($name)) {
                            $name = $user->handle;
                        }
                        $user->setTitle($name);
                        if (!\Idno\Entities\User::get()) {
                            $user->setAdmin(true);
                        }
                        $user->save();
                    } else {
                        if (empty($handle)) {
                            \Idno\Core\site()->session()->addMessage("You can't have an empty handle.");
                        } else if (strlen($handle) > 32) {
                            \Idno\Core\site()->session()->addMessage("Your handle is too long.");
                        } else if (substr_count($handle, '/')) {
                            \Idno\Core\site()->session()->addMessage("Handles can't contain a slash ('/') character.");
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
                    \Idno\Core\site()->session()->addMessage("You've registered! You're ready to get started. Why not add some profile information?");
                    \Idno\Core\site()->session()->logUserOn($user);
                    if (empty($onboarding)) {
                        $this->forward($user->getURL());
                    } else {
                        $this->forward(\Idno\Core\site()->config()->getURL() . 'begin/profile');
                    }
                } else {
                    \Idno\Core\site()->session()->addMessage("We couldn't register you.");
                    $this->forward($_SERVER['HTTP_REFERER']);
                }

            }

        }

    }