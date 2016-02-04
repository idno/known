<?php

/**
 * Allow logging, with toggle support
 *
 * @package idno
 * @subpackage core
 */

namespace Idno\Core {

    class KnownLogger implements \Psr\Log\LoggerInterface {

        public $loglevel_filter = 4;
        private $identifier;
        private $contexts = [];

        /**
         * Create a basic logger to log to the PHP log.
         *
         * @param type $loglevel_filter Log levels to show 0 - off, 1 - errors, 2 - errors & warnings, 3 - errors, warnings and info, 4 - 3 + debug
         * @param type $identifier Identify this site in the log (defaults to current domain)
         */
        public function __construct($loglevel_filter = 0, $identifier = null) {
            if (!$identifier)
                $identifier = \Idno\Core\Idno::site()->config->host;
            if (isset(\Idno\Core\Idno::site()->config->loglevel)) {
                $loglevel_filter = \Idno\Core\Idno::site()->config->loglevel;
            }

            $this->loglevel_filter = $loglevel_filter;
            $this->identifier = $identifier;
            $this->contexts = [];
        }

        /**
         * Write a message to the log.
         * @param type $message
         * @param type $level
         */
        public function log($level = 3, $message, array $context = array()) {

            // See if this message isn't filtered out
            if ($level <= $this->loglevel_filter) {

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
                if ($level == 1)
                    $level = "ERROR";
                if ($level == 2)
                    $level = "WARNING";
                if ($level == 3)
                    $level = "INFO";
                if ($level == 4)
                    $level = "DEBUG";

                // Logging contexts

                if (!empty($context)) {
                    $context = ' [' . implode(';', $this->contexts) . ']';
                }

                error_log("Known ({$this->identifier}$context): $level - $message{$trace}");
            }
        }

        /**
         * Write emergency messsage to error log
         *
         * @param string $message
         * @param array $context
         */
        public function emergency($message, array $context = array()) {
            $this->log($message, 1, $context);
        }

        /**
         * Write alert message to warning log
         * @param string $message
         * @param array $context
         */
        public function alert($message, array $context = array()) {
             $this->log($message, 2, $context);
        }
        
        
        /**
         * Write alert message to info log
         * @param string $message
         * @param array $context
         */
        public function notice($message, array $context = array()) {
            $this->log($message, 3, $context);
        }

        /**
         * Writes critical message to error log
         * @param string $message
         * @param array $context
   
         */
        public function critical($message, array $context = array()) {
            $this->log($message, 1, $context);
        }

        /**
         * writes error message to log
         * @param type $message
         * @param array $context
         */
        public function error($message, array $context = array()) {
            $this->log($message, 1, $context);
        }

        /**
         * writes warning message to log
         * @param type $message
         * @param array $context
         */
        public function warning($message, array $context = array()) {
            $this->log($message, 2, $context);
        }

        /**
         * writes info message to log
         * @param type $message
         * @param array $context
         */
        public function info($message, array $context = array()) {
            $this->log($message, 3, $context);
        }

        /**
         * writes debug message to log
         * @param type $message
         * @param array $context
         */
        public function debug($message, array $context = array()) {
            $this->log($message, 4, $context);
        }

    }

    define('LOGLEVEL_OFF', 0);
    define('LOGLEVEL_ERROR', 1);
    define('LOGLEVEL_WARNING', 2);
    define('LOGLEVEL_INFO', 3);
    define('LOGLEVEL_DEBUG', 4);
}
