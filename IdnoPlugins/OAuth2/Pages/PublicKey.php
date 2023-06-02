<?php

namespace IdnoPlugins\OAuth2\Pages {

    use IdnoPlugins\OAuth2\OAuth2Exception;
    use IdnoPlugins\OAuth2\Application;

    class PublicKey extends \Idno\Common\Page {

        function getContent() {
            
            header('Content-Type: text/plain');
            
            // Are we loading an entity?
            if (!empty($this->arguments)) {
                $object = Application::getOne(['key' => $this->arguments[0]]);
            }
            if (empty($object)) {
                throw new OAuth2Exception(\Idno\Core\Idno::site()->language()->_("The Application for this client id could not be found"));
            }

            $publickey = $object->getPublicKey();
            if (empty($publickey)) {
                throw new OAuth2Exception(\Idno\Core\Idno::site()->language()->_("No public key could be found"));
            }
            
            echo $publickey;
        }

    }

}