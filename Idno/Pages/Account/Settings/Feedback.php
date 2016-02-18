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
                $t        = \Idno\Core\Idno::site()->template();
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

                    $results    = Webservice::post('https://withknown.com/vendor-services/feedback/', array(
                        'url'     => \Idno\Core\Idno::site()->config()->getURL(),
                        'title'   => \Idno\Core\Idno::site()->config()->getTitle(),
                        'version' => \Idno\Core\Idno::site()->getVersion(),
                        'public'  => \Idno\Core\Idno::site()->config()->isPublicSite(),
                        'hub'     => \Idno\Core\Idno::site()->config()->known_hub,
                        'email'   => $email,
                        'message' => $message
                    ));

                    \Idno\Core\Idno::site()->session()->addMessage("Thanks! We received your feedback.");

                }

                $this->forward(\Idno\Core\Idno::site()->config()->getURL() . 'account/settings/feedback/confirm/');

            }

        }

    }