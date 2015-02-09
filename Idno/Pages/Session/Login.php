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

                // If we're somehow here but logged in, move to the front page
                if (\Idno\Core\site()->session()->isLoggedOn()) {
                    $this->forward();
                }

                $fwd = $this->getInput('fwd'); // Forward to a new page?
                if ($fwd == \Idno\Core\site()->config()->url . 'session/login') {
                    $fwd = '';
                }
                $t        = \Idno\Core\site()->template();
                $t->body  = $t->__(array('fwd' => $fwd))->draw('session/login');
                $t->title = 'Sign in';
                $t->drawPage();
            }

            function postContent()
            {
                /*if (!\Idno\Common\Page::isSSL() && !\Idno\Core\site()->config()->disable_cleartext_warning) {
                    \Idno\Core\site()->session()->addErrorMessage("Warning: Access credentials were sent over a non-secured connection! To disable this warning set disable_cleartext_warning in your config.ini");
                }*/
                    
                $fwd = $this->getInput('fwd'); // Forward to a new page?
                if (empty($fwd)) {
                    $fwd = \Idno\Core\site()->config()->url;
                }

                if ($user = \Idno\Entities\User::getByHandle($this->getInput('email'))) {
                } else if ($user = \Idno\Entities\User::getByEmail($this->getInput('email'))) {
                } else {
                    \Idno\Core\site()->triggerEvent('login/failure/nouser', array('method' => 'password', 'credentials' => array('email' => $this->getInput('email'))));
                    $this->setResponse(401);
                }

                if ($user instanceof \Idno\Entities\User) {
                    if ($user->checkPassword(trim($this->getInput('password')))) {
                        \Idno\Core\site()->triggerEvent('login/success', array('user' => $user)); // Trigger an event for auditing
                        \Idno\Core\site()->session()->logUserOn($user);
                        $this->forward($fwd);
                    } else {
                        \Idno\Core\site()->session()->addErrorMessage("Oops! It looks like your password isn't correct. Please try again.");
                        \Idno\Core\site()->triggerEvent('login/failure', array('user' => $user));
                        $this->forward(\Idno\Core\site()->config()->getDisplayURL() . 'session/login/');
                    }
                } else {
                    \Idno\Core\site()->session()->addErrorMessage("Oops! We couldn't find your username or email address. Please check you typed it correctly and try again.");
                    $this->forward(\Idno\Core\site()->config()->getDisplayURL() . 'session/login/');
                }
            }

        }

    }