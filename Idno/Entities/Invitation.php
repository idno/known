<?php

    /**
     * Site invitation representation
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Entities {

        use Idno\Core\Email;

        class Invitation extends \Idno\Common\Entity
        {

            function __construct()
            {
                $this->generateCode();

                parent::__construct();
            }

            /**
             * Generates the code associated with this invitation
             */
            function generateCode()
            {
                if (\Idno\Core\site()->session()->isLoggedOn()) {
                    $email = \Idno\Core\site()->session()->currentUser()->email;
                } else {
                    $email = base64_encode(time() . rand(0, 99999));
                }
                $this->code = md5(time() . rand(0, 9999) . $email);
            }

            /**
             * Associates this invitation with a particular email address; returns false if the address is invalid
             * @param $email
             * @return bool
             */
            function associateWithEmail($email)
            {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $this->email = $email;

                    return true;
                }

                return false;
            }

            /**
             * Saves this invitation and sends it to the appropriate email address
             * @param $email
             * @return bool|int
             */
            function sendToEmail($email)
            {
                if ($this->associateWithEmail($email)) {
                    $this->save();
                    $message = new Email();
                    $message->addTo($email);
                    $message->setSubject(\Idno\Core\site()->session()->currentUser()->getTitle() . " has invited you to join " . \Idno\Core\site()->config()->title . '!');
                    $message->setHTMLBodyFromTemplate('account/invite', ['email' => $email, 'code' => $this->code, 'inviter' => \Idno\Core\site()->session()->currentUser()->getTitle()]);

                    return $message->send();
                }

                return false;
            }

            /**
             * Retrieves an invitation associated with a particular email address
             * @param $email
             * @return bool
             */
            static function getByEmail($email)
            {
                if ($result = \Idno\Core\site()->db()->getObjects(get_called_class(), array('email' => $email), null, 1)) {
                    foreach ($result as $row) {
                        return $row;
                    }
                }

                return false;
            }

            /**
             * Retrieves an invitation associated with a particular email address and code.
             * @param $email
             * @param $code
             * @return bool
             */
            static function getByEmailAndCode($email, $code)
            {
                if ($result = \Idno\Core\site()->db()->getObjects(get_called_class(), array('email' => $email, 'code' => $code), null, 1)) {
                    foreach ($result as $row) {
                        return $row;
                    }
                }

                return false;
            }

            /**
             * Validates an email address / invitation code combination (or returns false if no such invitation exists).
             * @param $email
             * @param $code
             * @return \Idno\Entities\Invitation|false
             */
            static function validate($email, $code)
            {
                if ($invitation = self::getByEmailAndCode($email, $code)) {
                    return $invitation;
                }

                return false;
            }
        }

    }