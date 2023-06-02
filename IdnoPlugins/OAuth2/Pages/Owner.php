<?php

namespace IdnoPlugins\OAuth2\Pages {

    class Owner extends \Idno\Pages\Session\CurrentUser {

        function getContent() {
            \Idno\Core\Idno::site()->template()->setTemplateType('json');

            $this->gatekeeper(); // Authenticate via access token
            
            parent::getContent();
        }

        function postContent() {
            $this->getContent();
        }

    }

}
