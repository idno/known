<?php

/**
 * Time and time manipulation functions.
 *
 * @package idno
 * @subpackage core
 */

namespace Idno\Common {

    class Time extends Component {

        /**
         * Convert a unix timestamp into an RFC2616 (HTTP) compatible date.
         * @param type $timestamp
         */
        public static function timestampToRFC2616($timestamp) {
            return gmdate('D, d M Y H:i:s T', (int) $timestamp);
        }

    }

}