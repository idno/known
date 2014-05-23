<?php

    namespace Idno\Core {

        use IdnoPlugins\Styles\Pages\Styles\Site;

        class Email extends \Idno\Common\Component
        {

            public $message;

            function init() {
                // Using SwiftMailer to establish a message
                require_once site()->config()->path . 'external/swiftmailer/lib/swift_required.php';
                $this->message = \Swift_Message::newInstance();
            }

            /**
             * Set the subject of the message
             * @param $subject
             */
            function setSubject($subject)
            {
                return $this->message->setSubject($subject);
            }

            /**
             * Set the "From" address of the message
             * @param $email The email address of the account
             * @param $name The name of the account
             * @return mixed
             */
            function setFrom($email, $name = '') {
                if (!empty($name)) {
                    return $this->message->addFrom([$name => $email]);
                }
                return $this->message->addFrom($email);
            }

            /**
             * Add a recipient
             * @param string $email The email address of the recipient
             * @param string $name The name of the recipient (optional)
             * @return mixed
             */
            function addTo($email, $name = '') {
                if (!empty($name)) {
                    return $this->message->addTo([$name => $email]);
                }
                return $this->message->addTo($email);
            }

            /**
             * Add a "reply to" message
             * @param $email
             * @param string $name
             * @return mixed
             */
            function setReplyTo($email, $name = '') {
                if (!empty($name)) {
                    return $this->message->addReplyTo([$name => $email]);
                }
                return $this->message->addReplyTo($email);
            }

            /**
             * Sets the HTML body of the message (optionally setting it inside the email pageshell as defined by the email template)
             * @param $body The formatted HTML body text of the message
             * @param bool $shell Should the message be placed inside the pageshell? Default: true
             * @return mixed
             */
            function setHTMLBody($body, $shell = true) {
                if ($shell) {
                    $t = site()->template();
                    $t->setTemplateType('email');
                    $message = $t->__(['body' => $body])->draw('shell');
                } else {
                    $message = $body;
                }
                return $this->message->setBody($message, 'text/html');
            }

            /**
             * Sets the plain text body of the message
             * @param string $body The body of the message
             * @return mixed
             */
            function setTextBody($body) {
                return $this->message->addPart($body, 'text/plain');
            }

            /**
             * Send the message
             * @return int
             */
            function send() {
                $transport = \Swift_SmtpTransport::newInstance();   // TODO: allow this to be extended to allow for external mail services
                $mailer = \Swift_Mailer::newInstance($transport);
                return $mailer->send($this->message);
            }

        }

    }