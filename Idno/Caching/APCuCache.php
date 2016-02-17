<?php

    namespace Idno\Caching {

        /**
         * Implement a persistent cache using APC User caching.
         */
        class APCuCache extends PersistentCache
        {

            public function delete($key)
            {
                apcu_dec('__known_apcu_size', strlen($this->load($key)));

                return apcu_delete($key);
            }

            public function load($key)
            {
                return apcu_fetch($key);
            }

            public function size()
            {
                return (int)$this->load('__known_apcu_size');
            }

            public function store($key, $value)
            {

                if (apcu_store($key, $value)) {
                    apcu_inc('__known_apcu_size', strlen($value));
                }

                return false;
            }

        }

    }