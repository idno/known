<?php

    /**
     * Administration page: email settings
     */

    namespace Idno\Pages\Admin {

        class EmailTest extends \Idno\Common\Page
        {

            function postContent()
            {
                $this->adminGatekeeper(); // Admins only

                $email = $this->getInput('to_email');

                try {
                    $message = new \Idno\Core\Email();
                    $message->addTo($email);
                    $message->setSubject("Test email from " . \Idno\Core\Idno::site()->config()->title . '!');
                    $message->setHTMLBodyFromTemplate('admin/emailtest');
                    $message->setTextBodyFromTemplate('admin/emailtest');

                    if ($message->send()) {
                        \Idno\Core\Idno::site()->session()->addMessage("Test email sent to $email");
                    } else {
                        \Idno\Core\Idno::site()->session()->addErrorMessage("There was a problem sending a test message to {$email}.");
                    }
                } catch (\Exception $e) {
                    \Idno\Core\Idno::site()->session()->addErrorMessage($e->getMessage());
                }

                $this->forward(\Idno\Core\Idno::site()->config()->getURL() . 'admin/email');
            }

        }

    }