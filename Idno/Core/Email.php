<?php

namespace Idno\Core {

    class Email extends \Idno\Common\Component
    {

        public $message;

        function init()
        {
            // Using SwiftMailer to establish a message
            try {
                $this->message = new \Swift_Message();
            } catch (\Exception $e) {
                \Idno\Core\Idno::site()->session()->addErrorMessage(\Idno\Core\Idno::site()->language()->_("Something went wrong and we couldn't create the email message to send."));
            }
        }

        /**
         * Set the subject of the message
         *
         * @param $subject
         */
        function setSubject($subject)
        {
            return $this->message->setSubject($subject);
        }

        /**
         * Set the "From" address of the message
         *
         * @param  $email The email address of the account
         * @param  $name  The name of the account
         * @return mixed
         */
        function setFrom($email, $name = '')
        {
            if (!empty($name)) {
                return $this->message->addFrom($email,$name);
            }

            return $this->message->addFrom($email);
        }

        /**
         * Add a recipient
         *
         * @param  string $email The email address of the recipient
         * @param  string $name  The name of the recipient (optional)
         * @return mixed
         */
        function addTo($email, $name = '')
        {
            if (!empty($name)) {
                return $this->message->addTo($email,$name);
            }

            return $this->message->addTo($email);
        }

        /**
         * Adds an email to the BCC list
         *
         * @param  $email
         * @return mixed
         */
        function addBcc($email)
        {
            return $this->message->addBcc($email);
        }

        /**
         * Add a "reply to" message
         *
         * @param  $email
         * @param  string $name
         * @return mixed
         */
        function setReplyTo($email, $name = '')
        {
            if (!empty($name)) {
                return $this->message->addReplyTo($email,$name);
            }

            return $this->message->addReplyTo($email);
        }

        /**
         * Given the name of a template and a set of variables to include, generates an HTML body and adds it to the message
         *
         * @param  $template_name
         * @param  array $vars
         * @return mixed
         */
        function setHTMLBodyFromTemplate($template_name, $vars = array(), array $shellVars = [])
        {
            $t = clone \Idno\Core\Idno::site()->template();
            $t->setTemplateType('email');
            $body = $t->__($vars)->draw($template_name);

            return $this->setHTMLBody($body, true, $shellVars);
        }

        /**
         * Sets the HTML body of the message (optionally setting it inside the email pageshell as defined by the email template)
         *
         * @param  $body      The formatted HTML body text of the message
         * @param  bool  $shell     Should the message be placed inside the pageshell? Default: true
         * @param  array $shellVars Variables to pass to the page shell template
         * @return mixed
         */
        function setHTMLBody($body, $shell = true, array $shellVars = [])
        {
            if ($shell) {
                $t = clone \Idno\Core\Idno::site()->template();
                $t->setTemplateType('email');
                $message = $t->__(array_merge(array('body' => $body), $shellVars))->draw('shell');
            } else {
                $message = $body;
            }

            return $this->message->setBody($message, 'text/html');
        }

        /**
         * Set the text only component of an email.
         *
         * @param  string $template_name
         * @param  string $vars
         * @return mixed
         */
        function setTextBodyFromTemplate($template_name, $vars = array())
        {
            $t = clone \Idno\Core\Idno::site()->template();
            $t->setTemplateType('email-text');
            $body = $t->__($vars)->draw($template_name);

            return $this->setTextBody($body);
        }

        /**
         * Sets the plain text body of the message
         *
         * @param  string $body The body of the message
         * @return mixed
         */
        function setTextBody($body)
        {
            return $this->message->addPart($body, 'text/plain');
        }

        /**
         * Send the message
         *
         * @return int
         */
        function send()
        {
            try {
                if ($smtp_host = \Idno\Core\Idno::site()->config()->smtp_host) {
                    $transport = new \Swift_SmtpTransport($smtp_host);
                    if ($smtp_username = \Idno\Core\Idno::site()->config()->smtp_username) {
                        $transport->setUsername($smtp_username);
                        if ($smtp_password = \Idno\Core\Idno::site()->config()->smtp_password) {
                            $transport->setPassword($smtp_password);
                        }
                    }
                } else {
                    $transport = new \Swift_SmtpTransport();
                }
                if (!empty(\Idno\Core\Idno::site()->config()->smtp_port)) {
                    $transport->setPort(\Idno\Core\Idno::site()->config()->smtp_port);
                }
                if (!empty(\Idno\Core\Idno::site()->config()->smtp_secure)) {
                    switch (\Idno\Core\Idno::site()->config()->smtp_secure) {
                        case 'tls':
                            $transport->setEncryption('tls');
                            break;
                        case 'ssl':
                            $transport->setEncryption('ssl');
                            break;
                    }
                }
                $mailer = new \Swift_Mailer($transport);

                // Set the "from" address
                if ($from_email = \Idno\Core\Idno::site()->config()->from_email) {
                    $this->message->setFrom($from_email, \Idno\Core\Idno::site()->config()->title);
                }

                return $mailer->send(\Idno\Core\Idno::site()->events()->triggerEvent('email/send', ['email' => $this], $this->message));

            } catch (\Exception $e) {
                // Lets log errors rather than silently drop them
                \Idno\Core\Idno::site()->logging()->error('Error sending mail', ['error' => $e->getMessage()]);
            }

            return 0;
        }

    }

}

