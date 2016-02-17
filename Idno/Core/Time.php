<?php

    /**
     * Time and time manipulation functions.
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Core {

        class Time extends \Idno\Common\Component
        {

            /**
             * Convert an epoch timestamp into an RFC2616 (HTTP) compatible date.
             * @param type $timestamp Optionally, an epoch timestamp. Defaults to the current time.
             */
            public static function timestampToRFC2616($timestamp = false)
            {
                if ($timestamp === false) {
                    $timestamp = time();
                }

                return gmdate('D, d M Y H:i:s T', (int)$timestamp);
            }

        }

    }