<?php

namespace Idno\Caching {

    /**
     * Store values in memory for the lifetime of script execution.
     * @deprecated Use ArrayCache
     */
    class MemoryCache
        extends ArrayCache
    {
        public function __construct()
        {
            trigger_error("MemoryCache has been deprecated, use ArrayCache");

            parent::__construct();
        }
    }
}

