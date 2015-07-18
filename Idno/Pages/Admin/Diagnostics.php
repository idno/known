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
                if ($this->xhr) {
                
                    $report = "Known Diagnostics: Build " . \Idno\Core\site()->version() . "\nDate: " . date('r') . "\n\n";
                    $report .= "*** WARNING: This report contains security sensitive information, take care how you send it to people! ***\n\n";
                    $report .= "Running config:\n---------------\n" . var_export(\Idno\Core\site()->config(), true) . "\n\n";
                    $report .= "\$_SESSION:\n----------\n" . var_export($_SESSION, true) . "\n\n";
                    $report .= "\$_SERVER:\n---------\n" . var_export($_SERVER, true) . "\n\n";

                    // Hook so other plugins and subsystems can add their own data to the report.
                    $report = \Idno\Core\site()->triggerEvent('diagnostics/generate', [], $report);
                    
                    echo $report; 
                    exit;
                } 
                else 
                {
                    $t        = \Idno\Core\site()->template();
                    $t->body  = $t->draw('admin/diagnostics');
                    $t->title = 'Diagnostics';
                    $t->drawPage();
                }
            }

        }
    }
?>