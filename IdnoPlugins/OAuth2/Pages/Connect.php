<?php

namespace IdnoPlugins\OAuth2\Pages {

    class Connect extends \Idno\Common\Page
    {

        function getContent()
        {
            $this->gatekeeper();

            $fwd = $this->getInput('fwd'); // return page
            $client_id = $this->getInput('client_id');
            $scope = $this->getInput('scope');

            $client = \IdnoPlugins\OAuth2\Application::getOne(['key' => $client_id]);
            if ($client) {

                $t = \Idno\Core\site()->template();
                $t->body = $t->__(array('fwd' => $fwd, 'client_id' => $client_id, 'scope' => $scope, 'client' => $client))->draw('oauth2/forms/connect');
                $t->title = 'Authorise connection...';
                $t->drawPage();
            }
            else 
            {
                throw new \Exception(\Idno\Core\Idno::site()->language()->_("Could not load client associated with %s", [$client_id]));
            }
        }

        function postContent()
        {
            $this->gatekeeper();

            $user = \Idno\Core\site()->session()->currentUser();

            $client_id = $this->getInput('client_id');
            $scope = $this->getInput('scope');

            $user->oauth2 = [
                $client_id => [
                    'scope' => $scope
                ]
            ];

            if ($user->save()) {
                $this->forward($this->getInput('fwd'));
            }

        }

    }

}
