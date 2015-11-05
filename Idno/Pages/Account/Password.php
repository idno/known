<?php

    /**
     * Recover a forgotten password
     */

    namespace Idno\Pages\Account {

        use Idno\Core\Email;
        use Idno\Entities\User;

        /**
         * Default class to serve the password recovery page
         */
        class Password extends \Idno\Common\Page
        {

            function getContent()
            {

                $this->reverseGatekeeper();
                $t = \Idno\Core\Idno::site()->template();

                if ($sent = $this->getInput('sent')) {
                    $t->body  = $t->draw('account/password/sent');
                    $t->title = 'Password recovery email sent';
                } else {
                    $t->body  = $t->draw('account/password');
                    $t->title = 'Recover password';
                }

                $t->drawPage();

            }

            function postContent()
            {

                $this->reverseGatekeeper();
                $email_address = $this->getInput('email');

                if ($user = User::getByEmail($email_address)) {

                    if ($auth_code = $user->addPasswordRecoveryCode()) {

                        $user->save(); // Save the recovery code to the user

                        $t = clone \Idno\Core\Idno::site()->template();
                        $t->setTemplateType('email');

                        $email = new Email();
                        $email->setSubject("Password reset");
                        $email->addTo($user->email);
                        $email->setHTMLBody($t->__(array('email' => $email_address, 'code' => $auth_code))->draw('account/password'));
                        $email->setTextBodyFromTemplate('account/password', array('email' => $email_address, 'code' => $auth_code));
                        $email->send();

                        $this->forward(\Idno\Core\Idno::site()->config()->getURL() . 'account/password/?sent=true');

                    }

                }
                \Idno\Core\Idno::site()->session()->addErrorMessage("Oh no! We couldn't find an account associated with that email address.");
                $this->forward(\Idno\Core\Idno::site()->config()->getURL() . 'account/password');

            }

        }

    }