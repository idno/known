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
                if (\Idno\Core\Idno::site()->session()->isLoggedOn()) {
                    $this->forward();
                }

                $fwd = $this->getInput('fwd'); // Forward to a new page?
                if ($fwd == \Idno\Core\Idno::site()->config()->url . 'session/login') {
                    $fwd = '';
                }
                $t        = \Idno\Core\Idno::site()->template();
                $t->body  = $t->__(array('fwd' => $fwd))->draw('account/login');
                $t->title = 'Sign in';
                $t->drawPage();
            }

            function postContent()
            {

                $fwd = $this->getInput('fwd'); // Forward to a new page?
                if (empty($fwd)) {
                    $fwd = \Idno\Core\Idno::site()->config()->url;
                }

                $this->referrerGatekeeper();

                if ($user = \Idno\Entities\User::getByHandle($this->getInput('email'))) {
                } else if ($user = \Idno\Entities\User::getByEmail($this->getInput('email'))) {
                } else {
                    \Idno\Core\Idno::site()->triggerEvent('login/failure/nouser', array('method' => 'password', 'credentials' => array('email' => $this->getInput('email'))));
                    $this->setResponse(401);
                }

                if ($user instanceof \Idno\Entities\User) {
                    if ($user->checkPassword(trim($this->getInput('password')))) {
                        \Idno\Core\Idno::site()->triggerEvent('login/success', array('user' => $user)); // Trigger an event for auditing
                        \Idno\Core\Idno::site()->session()->logUserOn($user);
                        $this->forward($fwd);
                    } else {
                        \Idno\Core\Idno::site()->session()->addErrorMessage("Oops! It looks like your password isn't correct. Please try again.");
                        \Idno\Core\Idno::site()->triggerEvent('login/failure', array('user' => $user));
                        $this->forward(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'session/login/?fwd=' . urlencode($fwd));
                    }
                } else {
                    \Idno\Core\Idno::site()->session()->addErrorMessage("Oops! We couldn't find your username or email address. Please check you typed it correctly and try again.");
                    $this->forward(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'session/login/?fwd=' . urlencode($fwd));
                }
            }

        }

    }