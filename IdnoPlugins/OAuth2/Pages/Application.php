<?php

namespace IdnoPlugins\OAuth2\Pages;


class Application extends \Idno\Common\Page {
    
    
    function getContent()
    {
        header('Content-type: application/json');
        
        if (!empty($this->arguments)) {
            $object = \IdnoPlugins\OAuth2\Application::getOne(['key' => $this->arguments[0]]);
        }
        if (empty($object)) {
            throw new \IdnoPlugins\OAuth2\OAuth2Exception(\Idno\Core\Idno::site()->language()->_("The Application for this client id could not be found"));
        }

        echo json_encode($object, JSON_PRETTY_PRINT);
    }
}