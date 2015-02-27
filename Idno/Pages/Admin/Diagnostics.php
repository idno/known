<?php

    /**
     * User administration
     */

    namespace Idno\Pages\Admin {

        class Diagnostics extends \Idno\Common\Page
        {

            function getContent()
            {
                $this->adminGatekeeper(); // Admins only

                // Create diagnostics report
                
                $report = "Known Diagnostics: Build " . \Idno\Core\site()->version() . "\nDate: " . date(r) . "\n\n";
                $report .= "Running config:\n---------------\n" . var_export(\Idno\Core\site()->config(), true) . "\n\n";
                $report .= "\$_SESSION:\n----------\n" . var_export($_SESSION, true) . "\n\n";
                $report .= "\$_SERVER:\n---------\n" . var_export($_SERVER, true) . "\n\n";
                
                $report = \Idno\Core\site()->triggerEvent('diagnostics/generate', [], $report);
                
                $t        = \Idno\Core\site()->template();
                $t->body  = $t->__(array('report' => $report))->draw('admin/diagnostics');
                $t->title = 'Diagnostics';
                $t->drawPage();

            }

        }
    }
?>