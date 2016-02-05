<?php

    /**
     * Allow logging, with toggle support
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Core {

        class Logging extends \Psr\Log\AbstractLogger implements \Psr\Log\LoggerInterface
        {
            public $loglevel_filter = 4;
            private $identifier;

            private $contexts = [];
                
            /**
             * Create a basic logger to log to the PHP log.
             *
             * @param type $loglevel_filter Log levels to show 0 - off, 1 - errors, 2 - errors & warnings, 3 - errors, warnings and info, 4 - 3 + debug
             * @param type $identifier Identify this site in the log (defaults to current domain)
             */
            public function __construct($loglevel_filter = 0, $identifier = null)
            {
                if (!$identifier) $identifier = \Idno\Core\Idno::site()->config->host;
                if (isset(\Idno\Core\Idno::site()->config->loglevel)) {
                    $loglevel_filter = \Idno\Core\Idno::site()->config->loglevel;
                }

                $this->loglevel_filter = $loglevel_filter;
                $this->identifier      = $identifier;
                $this->contexts        = [];
            }



            /**
             * Write a message to the log.
             * @param string $level
             * @param string $level
             * @param string $context
             */
            public function log($level, $message ,$context=array())
            {

                // See if this message isn't filtered out
                if ($level <= $this->loglevel_filter) {
                    
                    $this->contexts = $context;
                    // Construct log message

                    // Trace for debug (when filtering is set to debug, always add a trace)
                    $trace = "";
                    if ($this->loglevel_filter == 4) {
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

                    // Logging contexts
                    $context = '';
                    if (!empty($this->contexts)) {
                        $context = ' ['.implode(';', $this->contexts).']';
                    }
                    
                    error_log("Known ({$this->identifier}$contexts): $level - $message{$trace}");
                }
            }
        }

        define('LOGLEVEL_OFF', 0);
        define('LOGLEVEL_ERROR', 1);
        define('LOGLEVEL_WARNING', 2);
        define('LOGLEVEL_INFO', 3);
        define('LOGLEVEL_DEBUG', 4);
    }
