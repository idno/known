<?php

/**
 * logging implimentation
 *
 * @package idno
 * @subpackage core
 */

namespace Idno\Core {
use Psr\Log\LoggerInterface;

    class Logging extends \Idno\Common\Component {

        public $logger;

        /**
         * Function to set the logger. 
         * To be used by plugin to override default logger.
         * @param LoggerInterface $logger
         */
        public function setLogger(LoggerInterface $logger) {
            $this->logger = $logger;
        }

        /**
         * Function to return the current logger instance
         * @return type
         */
        public function getLogger() {
            return $this->logger;
        }

        /**
         * System is unusable.
         *
         * @param string $message
         * @param array $context
         * @return null
         */
        public function emergency($message, array $context = array()) {
            return $this->logger->emergency();
        }

        /**
         * Action must be taken immediately.
         *
         * Example: Entire website down, database unavailable, etc. This should
         * trigger the SMS alerts and wake you up.
         *
         * @param string $message
         * @param array $context
         * @return null
         */
        public function alert($message, array $context = array()) {
            return $this->logger->alert();
        }

        /**
         * Critical conditions.
         *
         * Example: Application component unavailable, unexpected exception.
         *
         * @param string $message
         * @param array $context
         * @return null
         */
        public function critical($message, array $context = array()) {
            return $this->logger->critical();
        }

        /**
         * Runtime errors that do not require immediate action but should typically
         * be logged and monitored.
         *
         * @param string $message
         * @param array $context
         * @return null
         */
        public function error($message, array $context = array()) {
            $this->logger->error();
        }

        /**
         * Exceptional occurrences that are not errors.
         *
         * Example: Use of deprecated APIs, poor use of an API, undesirable things
         * that are not necessarily wrong.
         *
         * @param string $message
         * @param array $context
         * @return null
         */
        public function warning($message, array $context = array()) {
            $this->logger->warning();
        }

        /**
         * Normal but significant events.
         *
         * @param string $message
         * @param array $context
         * @return null
         */
        public function notice($message, array $context = array()) {
            $this->logger->notice();
        }

        /**
         * Interesting events.
         *
         * Example: User logs in, SQL logs.
         *
         * @param string $message
         * @param array $context
         * @return null
         */
        public function info($message, array $context = array()) {
            $this->logger->info();
        }

        /**
         * Detailed debug information.
         *
         * @param string $message
         * @param array $context
         * @return null
         */
        public function debug($message, array $context = array()) {
            $this->logger->debug();
        }

        /**
         * Logs with an arbitrary level.
         *
         * @param mixed $level
         * @param string $message
         * @param array $context
         * @return null
         */
        public function log($level, $message, array $context = array()) {
            $this->logger->log();
        }

    }

}