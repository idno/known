<?php

    namespace IdnoPlugins\Bridgy\Pages {

        use Idno\Common\Page;

        class Disabled extends Page
        {

            function getContent()
            {
                $user = \Idno\Core\Idno::site()->session()->currentUser();
                // these will be set if this is a callback from bridgy
                $service = $this->getInput('service');
                $bresult = $this->getInput('result');

                if ($user && $service && $bresult == 'success') {
                    // update the user's bridgy-connection status for this service
                    $user->bridgy[$service] = array(
                        'status' =>  'disabled',
                        'user' => $this->getInput('user'),
                        'key' => $this->getInput('key'));
                    $user->save();
                }

                $this->forward(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'account/bridgy/');
            }

        }

    }