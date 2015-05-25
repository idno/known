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
                    $message->setSubject("Test email from " . \Idno\Core\site()->config()->title . '!');
                    $message->setHTMLBodyFromTemplate('admin/emailtest');
                    $message->setTextBodyFromTemplate('admin/emailtest');

                    if ($message->send()) {
                        \Idno\Core\site()->session()->addMessage("Test email sent to $email");
                    } else {
                        \Idno\Core\site()->session()->addErrorMessage("There was a problem sending a test message to {$email}.");
                    }
                } catch (\Exception $e) {
                    \Idno\Core\site()->session()->addErrorMessage($e->getMessage());
                }

                $this->forward(\Idno\Core\site()->config()->getURL() . 'admin/email');
            }

        }

    }