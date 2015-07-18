<?php

    /**
     * User administration
     */

    namespace Idno\Pages\Admin {

        class Diagnostics extends \Idno\Common\Page
        {
            
            /**
             * Quickly collect basic information about
             */
            protected function getBasics() {
                $basics = [
                    'status' => 'Ok',
                    'report' => [],
                ];
                
                // Check PHP version (sometimes install can be
                if (version_compare(phpversion(), '5.4') >= 0) {
                    $basics['report']['php-version'] = [
                        'status' => 'Ok'
                    ];
                } else {
                    $basics['report']['php-version'] = [
                        'status' => 'Failure',
                        'message' => 'You are running PHP version ' . phpversion() . ', which cannot run Known. You may need to ask your server administrator to upgrade PHP for you.'
                    ];
                    $basics['status'] = 'Failure';
                }
                
                // Check installed extensions
                $basics['report']['php-extensions'] = ['status' => 'Ok', 'message' => 'PHP Extension(s): '];
                foreach (['curl','date','dom','gd','json','libxml','mbstring','mysql','reflection','session','simplexml'] as $extension) {
                    if (!extension_loaded($extension)) {
                        $basics['report']['php-extensions']['message'] .= "$extension, ";
                        $basics['report']['php-extensions']['status'] = 'Failure';
                        $basics['status'] = 'Failure';
                    }
                }
                $basics['report']['php-extensions']['message'] = trim($basics['report']['php-extensions']['message'], ' ,') . ' missing.';
                
                return $basics;
            }

            function getContent()
            {
                $this->adminGatekeeper(); // Admins only

                // Generate basic diagnostics for report
                $basics = $this->getBasics();
                
                // Create diagnostics report
                if ($this->xhr) {
                
                    $report = "Known Diagnostics: Build " . \Idno\Core\site()->version() . "\nDate: " . date('r') . "\n\n";
                    $report .= "*** WARNING: This report contains security sensitive information, take care how you send it to people! ***\n\n";
                    $report .= "Basics:\n-------\n\n";
                    
                    if ($basics['status']!='Ok') {
                        foreach ($basics['report'] as $item => $details) {
                            $report .= "$item : {$details['status']}";
                            if ($details['status']!='Ok') {
                                $report .= " - {$details['message']}\n";
                            }
                            $report .= "\n";
                        }
                    } else {
                        $report .= "Basic checks on installation discovered no problems.\n\n";
                    }
                    
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
                    $t->body  = $t->__(['basics' => $basics])->draw('admin/diagnostics');
                    $t->title = 'Diagnostics';
                    $t->drawPage();
                }
            }

        }
    }
?>