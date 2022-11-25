<?php

namespace IdnoPlugins\OAuth2\Pages;

class WellKnown extends \Idno\Common\Page {
    
    
    function getContent()
    {
        header('Content-type: application/json');

        echo json_encode(\IdnoPlugins\OAuth2\Main::getWellKnown(), JSON_PRETTY_PRINT);
    }
}