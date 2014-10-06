<?php

    /**
     * Change user settings
     */

    namespace Idno\Pages\Account\Settings {

        use Idno\Core\Webservice;

        class Feedback extends \Idno\Common\Page
        {

            function getContent()
            {
                $this->createGatekeeper(); // Logged-in only please
                $t        = \Idno\Core\site()->template();
                $t->body  = $t->draw('account/settings/feedback');
                $t->title = 'Send feedback';
                $t->drawPage();
            }

            function postContent()
            {
                $this->createGatekeeper(); // Logged-in only please

                $email   = $this->getInput('email');
                $message = $this->getInput('message');

                if (!empty($email) && !empty($message)) {

                    $web_client = new Webservice();
                    $results    = $web_client->post('http://withknown.com/vendor-services/feedback/', [
                        'url'     => \Idno\Core\site()->config()->getURL(),
                        'title'   => \Idno\Core\site()->config()->getTitle(),
                        'version' => \Idno\Core\site()->getVersion(),
                        'public'  => \Idno\Core\site()->config()->isPublicSite(),
                        'hub'     => \Idno\Core\site()->config()->known_hub,
                        'email'   => $email,
                        'message' => $message
                    ]);

                    \Idno\Core\site()->session()->addMessage("Thanks! We received your feedback.");

                }

                $this->forward(\Idno\Core\site()->config()->getURL() . 'account/settings/feedback/confirm/');

            }

        }

    }