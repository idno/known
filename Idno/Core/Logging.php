<?php

    /**
     * Allow logging, with toggle support
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Core {

        class Logging extends \Idno\Common\Component
        {
            public $loglevel_filter;
            private $identifier;

            /**
             * Create a basic logger to log to the PHP log.
             *
             * @param type $loglevel_filter Log levels to show 0 - off, 1 - errors, 2 - errors & warnings, 3 - errors, warnings and info, 4 - 3 + debug
             * @param type $identifier Identify this site in the log (defaults to current domain)
             */
            public function __construct($loglevel_filter = 0, $identifier = null)
            {
                if (!$identifier) $identifier = \Idno\Core\site()->config->host;
                if (isset(\Idno\Core\site()->config->loglevel)) {
                    $loglevel_filter = \Idno\Core\site()->config->loglevel;
                }

                $this->loglevel_filter = $loglevel_filter;
                $this->identifier      = $identifier;
            }

            /**
             * Write a message to the log.
             * @param type $message
             * @param type $level
             */
            public function log($message, $level = 3)
            {

                // See if this message isn't filtered out
                if ($level <= $this->loglevel_filter) {

                    // Construct log message

                    // Trace for debug
                    $trace = "";
                    if ($level == 4) {
                        $backtrace = @debug_backtrace(false, 2);
                        if ($backtrace) {
                            // Never show this
                            $backtrace = $backtrace[0];

                            $trace = " [{$backtrace['file']}:{$backtrace['line']}]";
                        }
                    }

                    // Level
                    if ($level == 1) $level = "ERROR";
                    if ($level == 2) $level = "WARNING";
                    if ($level == 3) $level = "INFO";
                    if ($level == 4) $level = "DEBUG";

                    error_log("Known ({$this->identifier}): $level - $message {$this->loglevel_filter}$trace");
                }
            }
        }

        define('LOGLEVEL_OFF', 0);
        define('LOGLEVEL_ERROR', 1);
        define('LOGLEVEL_WARNING', 2);
        define('LOGLEVEL_INFO', 3);
        define('LOGLEVEL_DEBUG', 4);
    }
