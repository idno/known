<?php

namespace Idno\Pages\Service\Vendor {

    class Messages extends \Idno\Common\Page {

        function getContent() {
            $this->adminGatekeeper(); // Admins only

            if ($messages = \Idno\Core\Idno::site()->getVendorMessages()) {
                echo json_encode($messages);
            }
        }

    }

}