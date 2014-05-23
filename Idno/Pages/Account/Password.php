<?php

    /**
     * Recover a forgotten password
     */

    namespace Idno\Pages\Account {
        use Idno\Entities\User;

        /**
         * Default class to serve the password recovery page
         */
        class Password extends \Idno\Common\Page
        {

            function getContent() {

                $this->reverseGatekeeper();
                $t        = \Idno\Core\site()->template();

                if ($sent = $this->getInput('sent')) {
                    $t->body  = $t->draw('account/password/sent');
                    $t->title = 'Password recovery email sent';
                } else {
                    $t->body  = $t->draw('account/password');
                    $t->title = 'Recover password';
                }

                $t->drawPage();

            }

            function postContent() {

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