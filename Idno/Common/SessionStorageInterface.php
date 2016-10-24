<?php

/**
 * Define something that can handle sessions.
 */

namespace Idno\Common {

    /**
     * Define a Known interface for storing Sessions
     */
    interface SessionStorageInterface {

        /**
         * Offer a session handler for the current session
         */
        public function handleSession();
    }

}