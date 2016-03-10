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

                    $report = "Known Diagnostics: Version " . \Idno\Core\Idno::site()->version() . "\nDate: " . date('r') . "\n\n";
                    $report .= "*** WARNING: This report contains sensitive information. Be careful about how you transmit it, and to whom. ***\n\n";
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

                    $config                       = \Idno\Core\Idno::site()->config();
                    $config->config['dbpass']     = '** REDACTED **';
                    $config->ini_config['dbpass'] = '** REDACTED **';

                    $report .= "Running config:\n---------------\n" . var_export($config, true) . "\n\n";
                    $report .= "\$_SESSION:\n----------\n" . var_export($_SESSION, true) . "\n\n";
                    $report .= "\$_SERVER:\n---------\n" . var_export($_SERVER, true) . "\n\n";

                    // Hook so other plugins and subsystems can add their own data to the report.
                    $report = \Idno\Core\Idno::site()->triggerEvent('diagnostics/generate', [], $report);

                    echo $report;
                    exit;
                } else {
                    $t        = \Idno\Core\Idno::site()->template();
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

                // Check SSL
                if (!\Idno\Core\Idno::site()->currentPage()->isSSL()) {
                    $basics['status']             = 'Failure';
                    $basics['report']['security'] = [
                        'status'  => 'Warning',
                        'message' => 'Your site doesn\'t seem to be loaded with HTTPS. We strongly recommend using HTTPS to make your site secure and protect your privacy.'
                    ];
                }

                // Check PHP version 
                if (version_compare(phpversion(), '5.5') >= 0) {
                    $basics['report']['php-version'] = [
                        'status' => 'Ok'
                    ];
                } else if (version_compare(phpversion(), '5.4') >= 0) {
                    $basics['status']             = 'Failure';
                    $basics['report']['php-version'] = [
                        'status'  => 'Warning',
                        'message' => 'You are running Known using a very old version of PHP (' . phpversion() . '), which is no longer supported. Although Known will currently still install, some features will not work, so you should upgrade soon. You may need to ask your server administrator to upgrade PHP for you.'
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
                foreach (['curl', 'date', 'dom', 'gd', 'json', 'libxml', 'mbstring', 'pdo', 'pdo_mysql', 'reflection', 'session', 'simplexml', 'openssl'] as $extension) {
                    if (!extension_loaded($extension)) {
                        $basics['report']['php-extensions']['message'] .= "$extension, ";
                        $basics['report']['php-extensions']['status'] = 'Failure';
                        $basics['status']                             = 'Failure';
                    }
                }
                $basics['report']['php-extensions']['message'] = trim($basics['report']['php-extensions']['message'], ' ,') . ' missing.';

                // Check upload directory (if set)
                $basics['report']['upload-path'] = ['status' => 'Ok'];
                $upload_path                     = \Idno\Core\Idno::site()->config()->uploadpath;
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
                    if (\Idno\Core\Idno::site()->config()->filesystem == 'local') {
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