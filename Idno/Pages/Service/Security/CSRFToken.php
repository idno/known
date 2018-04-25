<?php

namespace Idno\Pages\Service\Security {

    class CSRFToken extends \Idno\Common\Page {

        function getContent() {
            
            $this->xhrGatekeeper();
            $this->setNoCache();
            
            $action = $this->getInput('url');
            if (empty($action))
                throw new \RuntimeException(\Idno\Core\Idno::site()->language()->_("URL missing"));
            
            //\Idno\Core\Idno::site()->logging()->debug("Updating token for $action");
            
            // Generate CSRF token for javascript queries (see #1727)
            $time = time();
            $token = \Idno\Core\Bonita\Forms::token($action, $time);
            
            header('Content-type: application/json');
            echo json_encode([
                'time' => $time,
                'token' => $token
            ]);
        }

    }

}