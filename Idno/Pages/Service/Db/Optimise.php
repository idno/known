<?php

namespace Idno\Pages\Service\Db {

    class Optimise extends \Idno\Common\Page {

        function getContent() {
            $this->adminGatekeeper(); // Admins only

            $lastOptTime = empty(\Idno\Core\Idno::site()->config()->dboptimized) ? 0 : \Idno\Core\Idno::site()->config()->dboptimized;
            if (($time = time()) - $lastOptTime > 24 * 60 * 60) {
                \Idno\Core\Idno::site()->logging()->info("Optimizing database tables. Last run " . date('Y-m-d H:i:s', $lastOptTime));
                \Idno\Core\Idno::site()->db()->optimize();
                \Idno\Core\Idno::site()->config()->dboptimized = $time;
                \Idno\Core\Idno::site()->config()->save();
                
                echo json_encode('optimised');
            }
        }

    }

}