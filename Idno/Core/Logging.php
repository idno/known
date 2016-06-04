<?php

/**
 * Allow logging, with toggle support
 *
 * @package idno
 * @subpackage core
 */

namespace Idno\Core {

    use Idno\Common\Component;
    use Psr\Log\LoggerInterface;
    use Psr\Log\LogLevel;

    class Logging extends Component implements LoggerInterface {

        public $loglevel_filter = 4;
        private $identifier;

        /**
         * Create a basic logger to log to the PHP log.
         *
         * @param type $loglevel_filter Log levels to show 0 - off, 1 - errors, 2 - errors & warnings, 3 - errors, warnings and info, 4 - 3 + debug
         * @param type $identifier Identify this site in the log (defaults to current domain)
         */
        public function __construct($loglevel_filter = 0, $identifier = null) {
            if (!$identifier)
                $identifier = Idno::site()->config->host;
            if (isset(Idno::site()->config->loglevel)) {
                $loglevel_filter = Idno::site()->config->loglevel;
            }

            $this->loglevel_filter = $loglevel_filter;
            $this->identifier = $identifier;

            // Set log to handle PHP errors
            set_error_handler(function ($errno, $errstr, $errfile, $errline, $errcontext) {
                                
                if (!(error_reporting() & $errno)) {
                    // This error code is not included in error_reporting
                    return;
                }
                
                $message = "PHP [{$errno}] {$errstr} in {$errfile}:{$errline}";
                
                // Pedantic mode
                if (\Idno\Core\Idno::site()->config()->pedantic_mode) {
                    $this->error($message);
                    throw new \ErrorException($message, 0, $errno, $errfile, $errline);
                }

                switch ($errno) {
                    case E_PARSE: 
                    case E_ERROR:
                    case E_CORE_ERROR:
                    case E_COMPILE_ERROR:
                    case E_USER_ERROR: $this->error($message); break;

                    case E_WARNING:
                    case E_CORE_WARNING:
                    case E_COMPILE_WARNING:
                    case E_USER_WARNING: $this->warning($message); break;

                    case E_STRICT:
                    case E_NOTICE:
                    case E_DEPRECATED:
                    case E_USER_DEPRECATED:
                    case E_STRICT:
                    case E_USER_NOTICE: $this->notice($message); break;

                    default:
                        $this->notice("Unknown error type: {$message}");
                        break;
                }

                /* Don't execute PHP internal error handler */
                //return true;
            });
        }

        /**
         * Sets the log level
         * @param $loglevel
         */
        public function setLogLevel($loglevel) {
            $this->loglevel_filter = $loglevel;
        }

        /**
         * Check whether a LogLevel meets the current loglevel_filter.
         * @param  string $level
         * @return boolean true if messages at this level should be logged
         */
        private function passesFilter($level) {
            switch ($level) {
                case LogLevel::EMERGENCY: case LogLevel::ALERT:
                case LogLevel::CRITICAL: case LogLevel::ERROR:
                    return $this->loglevel_filter >= LOGLEVEL_ERROR;
                case LogLevel::WARNING:
                    return $this->loglevel_filter >= LOGLEVEL_WARNING;
                case LogLevel::NOTICE: case LogLevel::INFO:
                    return $this->loglevel_filter >= LOGLEVEL_INFO;
                case LogLevel::DEBUG:
                    return $this->loglevel_filter >= LOGLEVEL_DEBUG;
            }
            return false;
        }

        /**
         * Write a message to the log.
         * @param string $level
         * @param string $message
         * @param array  $context
         */
        public function log($level, $message = LOGLEVEL_INFO, array $context = array()) {
            // backward compatibility
            if (is_string($level) && is_int($message)) {
                // TODO in a future version, warn that this
                // calling style is deprecated and will eventually
                // go away.
                $temp = $level;
                if ($message === LOGLEVEL_ERROR)
                    $level = LogLevel::ERROR;
                if ($message === LOGLEVEL_WARNING)
                    $level = LogLevel::WARNING;
                if ($message === LOGLEVEL_INFO)
                    $level = LogLevel::INFO;
                if ($message === LOGLEVEL_DEBUG)
                    $level = LogLevel::DEBUG;
                $message = $temp;
            }

            // See if this message isn't filtered out
            if ($this->passesFilter($level)) {

                // Construct log message
                // Trace for debug (when filtering is set to debug, always add a trace)
                $trace = "";
                if ($this->loglevel_filter == LOGLEVEL_DEBUG) {
                    $backtrace = @debug_backtrace(false, 3);
                    foreach (array_reverse($backtrace) as $frame) {
                        if (isset($frame['class']) && isset($frame['file']) && isset($frame['line']) && $frame['class'] !== 'Idno\Core\Logging') {
                            $trace = " [{$frame['file']}:{$frame['line']}]";
                            break;
                        }
                    }
                }

                if ($context) {
                    // borrowed from Monolog's LineFormatter
                    if (is_bool($context)) {
                        $context = var_export($context, true);
                    } else if (is_scalar($context)) {
                        $context = (string) $context;
                    } else {
                        $context = json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    }
                    $message .= ' ' . $context;
                }

                error_log("Known ({$this->identifier}): $level - $message{$trace}");
            }
        }

        /**
         * System is unusable.
         *
         * @param string $message
         * @param array  $context
         *
         * @return null
         */
        public function emergency($message, array $context = array()) {
            $this->log(LogLevel::EMERGENCY, $message, $context);
        }

        /**
         * Action must be taken immediately.
         *
         * Example: Entire website down, database unavailable, etc. This should
         * trigger the SMS alerts and wake you up.
         *
         * @param string $message
         * @param array  $context
         *
         * @return null
         */
        public function alert($message, array $context = array()) {
            $this->log(LogLevel::ALERT, $message, $context);
        }

        /**
         * Critical conditions.
         *
         * Example: Application component unavailable, unexpected exception.
         *
         * @param string $message
         * @param array  $context
         *
         * @return null
         */
        public function critical($message, array $context = array()) {
            $this->log(LogLevel::CRITICAL, $message, $context);
        }

        /**
         * Runtime errors that do not require immediate action but should typically
         * be logged and monitored.
         *
         * @param string $message
         * @param array  $context
         *
         * @return null
         */
        public function error($message, array $context = array()) {
            $this->log(LogLevel::ERROR, $message, $context);
        }

        /**
         * Exceptional occurrences that are not errors.
         *
         * Example: Use of deprecated APIs, poor use of an API, undesirable things
         * that are not necessarily wrong.
         *
         * @param string $message
         * @param array  $context
         *
         * @return null
         */
        public function warning($message, array $context = array()) {
            $this->log(LogLevel::WARNING, $message, $context);
        }

        /**
         * Normal but significant events.
         *
         * @param string $message
         * @param array  $context
         *
         * @return null
         */
        public function notice($message, array $context = array()) {
            $this->log(LogLevel::NOTICE, $message, $context);
        }

        /**
         * Interesting events.
         *
         * Example: User logs in, SQL logs.
         *
         * @param string $message
         * @param array  $context
         *
         * @return null
         */
        public function info($message, array $context = array()) {
            $this->log(LogLevel::INFO, $message, $context);
        }

        /**
         * Detailed debug information.
         *
         * @param string $message
         * @param array  $context
         *
         * @return null
         */
        public function debug($message, array $context = array()) {
            $this->log(LogLevel::DEBUG, $message, $context);
        }

    }

    define('LOGLEVEL_OFF', 0);
    define('LOGLEVEL_ERROR', 1);
    define('LOGLEVEL_WARNING', 2);
    define('LOGLEVEL_INFO', 3);
    define('LOGLEVEL_DEBUG', 4);
}
