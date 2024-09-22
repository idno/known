<?php

namespace Idno\Pages\Service\Vendor {

    class Messages extends \Idno\Common\Page
    {

        function getContent()
        {
            $this->adminGatekeeper(); // Admins only
            $this->setNoCache();

            if ($messages = \Idno\Core\Vendor::getMessages()) {
                \Idno\Core\Idno::site()->response()->setJsonContent(json_encode($messages));
            }
        }

    }

}
