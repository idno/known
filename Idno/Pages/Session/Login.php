<?php

    /**
     * Defines built-in log in functionality
     */

    namespace Idno\Pages\Session {

        /**
         * Default class to serve the homepage
         */
        class Login extends \Idno\Common\Page
        {

            function getContent()
            {
                $t        = \Idno\Core\site()->template();
                $t->body  = $t->draw('session/login');
                $t->title = 'Sign in';
                $t->drawPage();
            }

            function postContent()
            {
                // TODO: change this to actual basic login, of course
                if ($user = \Idno\Entities\User::getByHandle($this->getInput('email'))) {
                } else if ($user = \Idno\Entities\User::getByEmail($this->getInput('email'))) {
                } else {
                    \Idno\Core\site()->triggerEvent('login/failure/nouser', ['method' => 'password', 'credentials' => ['email' => $this->getInput('email')]]);
                    $this->setResponse(401);
                    $this->forward('/session/login');
                }

                if ($user instanceof \Idno\Entities\User) {
                    if ($user->checkPassword($this->getInput('password'))) {
                        \Idno\Core\site()->triggerEvent('login/success', ['user' => $user]); // Trigger an event for auditing
                        \Idno\Core\site()->session()->logUserOn($user);
                        $this->forward();
                    } else {
                        \Idno\Core\site()->session()->addMessage("Oops! It looks like your password isn't correct. Please try again.");
                        \Idno\Core\site()->triggerEvent('login/failure', ['user' => $user]);
                    }
                } else {
                    \Idno\Core\site()->session()->addMessage("Oops! We couldn't find your username or email address. Please check you typed it correctly and try again.");
                }
            }

        }

    }