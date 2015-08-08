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

                // Generate basic diagnostics for report
                $basics = $this->getBasics();

                // Create diagnostics report
                if ($this->xhr) {

                    $report = "Known Diagnostics: Version " . \Idno\Core\site()->version() . "\nDate: " . date('r') . "\n\n";
                    $report .= "*** WARNING: This report contains sensitive information. Be careful about who and how you transmit it. ***\n\n";
                    $report .= "Basics:\n-------\n\n";

                    if ($basics['status'] != 'Ok') {
                        foreach ($basics['report'] as $item => $details) {
                            $report .= "$item : {$details['status']}";
                            if ($details['status'] != 'Ok') {
                                $report .= " - {$details['message']}\n";
                            }
                            $report .= "\n";
                        }
                    } else {
                        $report .= "Basic checks on installation discovered no problems.\n\n";
                    }

                    $config                       = \Idno\Core\site()->config();
                    $config->config['dbpass']     = '** REDACTED **';
                    $config->ini_config['dbpass'] = '** REDACTED **';

                    $report .= "Running config:\n---------------\n" . var_export($config, true) . "\n\n";
                    $report .= "\$_SESSION:\n----------\n" . var_export($_SESSION, true) . "\n\n";
                    $report .= "\$_SERVER:\n---------\n" . var_export($_SERVER, true) . "\n\n";

                    // Hook so other plugins and subsystems can add their own data to the report.
                    $report = \Idno\Core\site()->triggerEvent('diagnostics/generate', [], $report);

                    echo $report;
                    exit;
                } else {
                    $t        = \Idno\Core\site()->template();
                    $t->body  = $t->__(['basics' => $basics])->draw('admin/diagnostics');
                    $t->title = 'Diagnostics';
                    $t->drawPage();
                }
            }

            /**
             * Quickly collect basic information about
             */
            protected function getBasics()
            {
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
                        'status'  => 'Failure',
                        'message' => 'You are running PHP version ' . phpversion() . ', which cannot run Known. You may need to ask your server administrator to upgrade PHP for you.'
                    ];
                    $basics['status']                = 'Failure';
                }

                // Check installed extensions
                $basics['report']['php-extensions'] = ['status' => 'Ok', 'message' => 'PHP Extension(s): '];
                foreach (['curl', 'date', 'dom', 'gd', 'json', 'libxml', 'mbstring', 'mysql', 'reflection', 'session', 'simplexml', 'openssl'] as $extension) {
                    if (!extension_loaded($extension)) {
                        $basics['report']['php-extensions']['message'] .= "$extension, ";
                        $basics['report']['php-extensions']['status'] = 'Failure';
                        $basics['status']                             = 'Failure';
                    }
                }
                $basics['report']['php-extensions']['message'] = trim($basics['report']['php-extensions']['message'], ' ,') . ' missing.';

                // Check upload directory (if set)
                $basics['report']['upload-path'] = ['status' => 'Ok'];
                $upload_path                     = \Idno\Core\site()->config()->uploadpath;
                if (!empty($upload_path)) {
                    if ($upload_path = realpath($upload_path)) {
                        if (substr($upload_path, -1) != '/' && substr($upload_path, -1) != '\\') {
                            $upload_path .= '/';
                        }
                        if (file_exists($upload_path) && is_dir($upload_path)) {
                            if (!is_readable($upload_path)) {
                                $basics['status'] = 'Failure';
                                $basics['report']['upload-path']['message'] .= 'We can\'t read data from ' . htmlspecialchars($upload_path) . ' - please check permissions and try again.';
                                $basics['report']['upload-path']['status'] = 'Failure';
                            }
                            if (!is_writable($upload_path)) {
                                $basics['status'] = 'Failure';
                                $basics['report']['upload-path']['message'] .= 'We can\'t write data to ' . htmlspecialchars($upload_path) . ' - please check it is writable by your web server user.';
                                $basics['report']['upload-path']['status'] = 'Failure';
                            }
                        } else {
                            $basics['status'] = 'Failure';
                            $basics['report']['upload-path']['message'] .= 'The upload path ' . htmlspecialchars($upload_path) . ' either doesn\'t exist or isn\'t a directory.';
                            $basics['report']['upload-path']['status'] = 'Failure';
                        }
                    } else {
                        $basics['status'] = 'Failure';
                        $basics['report']['upload-path']['message'] .= 'The upload path ' . htmlspecialchars($upload_path) . ' either doesn\'t exist or isn\'t a directory.';
                        $basics['report']['upload-path']['status'] = 'Failure';
                    }
                } else {
                    // We don't have an upload path set, do we need one?
                    if (\Idno\Core\site()->config()->filesystem == 'local') {
                        $basics['status'] = 'Failure';
                        $basics['report']['upload-path']['message'] .= "Your file system is set to 'local' but you have not specified an upload path (uploadpath = ...;) in your config.ini";
                        $basics['report']['upload-path']['status'] = 'Failure';
                    }
                }

                return $basics;
            }

        }
    }
?>