<?php

namespace Idno\Pages\Service\Security {

    class CSRFToken extends \Idno\Common\Page {

        function getContent() {
            
            $this->xhrGatekeeper();
            
            $action = $this->getInput('url');
            if (empty($action))
                throw new \RuntimeException("URL missing");
            
            // Generate CSRF token for javascript queries (see #1727)
            $action = $this->currentUrl();
            $time = time();
            $token = \Bonita\Forms::token($action, $time);
            
            header('Content-type: application/json');
            echo json_encode([
                'time' => $time,
                'token' => $token
            ]);
        }

    }

}