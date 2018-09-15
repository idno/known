<?php

/**
 * Cron functionality.
 * 
 * This requires AsynchronousQueue support, and a service installed to process each queue.
 *
 * @package idno
 * @subpackage core
 */

namespace Idno\Core {

    class Cron extends \Idno\Common\Component {

        /**
         * Most common time periods
         */
        public static $events = [
            'minute' => 60, // 60
            'hourly' => 3600, // 3600
            'daily' => 86400 // 86400
        ];

    }

}
