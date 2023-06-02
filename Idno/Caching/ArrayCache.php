<?php

namespace Idno\Caching {

    use Symfony\Component\Cache\Adapter\ArrayAdapter;

    /**
     * Store values in memory for the lifetime of script execution.
     */
    class ArrayCache
        extends EphemeralCache
    {
        public function __construct()
        {
            parent::__construct();

            $this->setCacheEngine(new ArrayAdapter());
        }
    }
}

