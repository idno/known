<?php

namespace Idno\Caching {

    use Symfony\Component\Cache\Adapter\ApcuAdapter;

    /**
     * Implement a persistent cache using APC User caching.
     */
    class APCuCache extends PersistentCache
    {
        public function __construct()
        {
            parent::__construct();

            $this->setCacheEngine(new ApcuAdapter());
        }

    }

}

