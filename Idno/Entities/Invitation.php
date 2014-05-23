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

            function __construct() {
                $this->generateCode();
                return parent::__construct();
            }

            /**
             * Generates the code associated with this invitation
             */
            function generateCode() {
                $this->code = md5(time() . rand(0,9999) . \Idno\Core\site()->session()->currentUser()->email);
            }

            /**
             * Associates this invitation with a particular email address; returns false if the address is invalid
             * @param $email
             * @return bool
             */
            function associateWithEmail($email) {
                if (filter_var(FILTER_VALIDATE_EMAIL, $email)) {
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
            function sendToEmail($email) {
                if ($this->associateWithEmail($email)) {
                    $this->save();
                    $message = new Email();
                    $message->addTo($email);
                    $message->setHTMLBodyFromTemplate('account/invite',['email' => $email, 'code' => $this->code, 'inviter' => \Idno\Core\site()->session()->currentUser()->getTitle()]);
                    return $message->send();
                }
                return false;
            }

            /**
             * Retrieves invitations associated with a particular email address
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
             * Validates an email address / invitation code combination (or returns false if no such invitation exists).
             * @param $email
             * @param $code
             * @return \Idno\Entities\Invitation|false
             */
            static function validate($email, $code) {
                if ($invitations = self::getByEmail($email)) {
                    foreach($invitations as $invitation) {
                        if ($invitation->code == $code) {
                            return $invitation;
                        }
                    }
                }
                return false;
            }

        }

    }