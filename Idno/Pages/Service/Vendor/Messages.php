<?php

namespace Idno\Pages\Service\Vendor {

    class Messages extends \Idno\Common\Page {

        function getContent() { 
            $this->adminGatekeeper(); // Admins only 
            $this->setNoCache();

            if ($messages = \Idno\Core\Vendor::getMessages()) {
                echo json_encode($messages);
            }
        }

    }

}