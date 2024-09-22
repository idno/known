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

                    $t        = \Idno\Core\Idno::site()->template();
                    $t->body  = $t->__(array('email' => $email, 'code' => $code))->draw('account/password/reset');
                    $t->title = \Idno\Core\Idno::site()->language()->_('Reset password');

                    $t->drawPage();
                    exit;

                }
            }

            \Idno\Core\Idno::site()->session()->addErrorMessage(\Idno\Core\Idno::site()->language()->_("The password reset code wasn't valid. They expire after three hours, so you might need to try again."));
            $this->forward(\Idno\Core\Idno::site()->config()->getURL() . 'account/password');

        }

        function postContent()
        {

            $this->reverseGatekeeper();
            $code      = $this->getInput('code');
            $email     = $this->getInput('email');
            $password  = trim($this->getInput('password'));
            $password2 = trim($this->getInput('password2'));

            if (\Idno\Entities\User::checkNewPasswordStrength($password) && $password == $password2) {
                if ($user = \Idno\Entities\User::getByEmail($email)) {

                    if ($code = $user->getPasswordRecoveryCode()) {

                        /* @var \Idno\Entities\User $user */
                        $user->setPassword($password);
                        $user->clearPasswordRecoveryCode();
                        $user->save(true);
                        \Idno\Core\Idno::site()->session()->addMessage(\Idno\Core\Idno::site()->language()->_("Your password was reset!"));

                    }
                }
            } else {
                \Idno\Core\Idno::site()->session()->addErrorMessage(\Idno\Core\Idno::site()->language()->_('Sorry, your passwords either don\'t match, or are too weak'));
                $this->forward(\Idno\Core\Idno::site()->request()->server->get('HTTP_REFERER'));
            }

        }

    }

}

