<?php

    /**
     * Reset a forgotten password
     */

    namespace Idno\Pages\Account\Password {

        /**
         * Default class to serve the password recovery page
         */
        class Reset extends \Idno\Common\Page
        {

            function getContent()
            {

                $this->reverseGatekeeper();
                $code  = $this->getInput('code');
                $email = $this->getInput('email');

                if ($user = \Idno\Entities\User::getByEmail($email)) {
                    if ($code = $user->getPasswordRecoveryCode()) {

                        $t        = \Idno\Core\site()->template();
                        $t->body  = $t->draw('account/password/reset');
                        $t->title = 'Reset password';

                        $t->drawPage();
                        exit;

                    }
                }

                \Idno\Core\site()->session()->addMessage("The password reset code wasn't valid. They expire after three hours, so you might need to try again.");
                $this->forward(\Idno\Core\site()->config()->getURL() . 'account/password');

            }

            function postContent()
            {

                $this->reverseGatekeeper();
                $email = $this->getInput('email');

                if ($user = User::getByEmail($email)) {

                    if ($auth_code = $user->addPasswordRecoveryCode()) {

                        // TODO: send email!

                    }

                }

            }

        }

    }