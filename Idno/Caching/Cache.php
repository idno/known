<?php

    namespace Idno\Caching {

        abstract class Cache
            implements \ArrayAccess
        {

            /**
             * Store or replace a value in the cache.
             *
             * @param $key string Identifier for this value
             * @param $value mixed Value to store
             * @return bool
             */
            abstract public function store($key, $value);

            /**
             * Retrieve a value from the store.
             * @param $key Key to retrieve
             * @return mixed|false
             */
            abstract public function load($key);

            /**
             * Remove a key from the cache.
             * @param The key
             * @return bool
             */
            abstract public function delete($key);

            /**
             * Return the number of keys currently stored.
             */
            abstract public function size();


            /* Object interface */
            public function __get($key)
            {
                return $this->load($key);
            }

            public function __set($key, $value)
            {
                return $this->store($key, $value);
            }

            public function __isset($key)
            {
                return (bool)$this->load($key);
            }

            public function __unset($key)
            {
                return $this->delete($key);
            }

            /* Array access interface */
            public function offsetGet($key)
            {
                return $this->load($key);
            }

            public function offsetSet($key, $value)
            {
                $this->store($key, $value);
            }

            public function offsetExists($key)
            {
                return (bool)$this->load($key);
            }

            public function offsetUnset($key)
            {
                return $this->delete($key);
            }
        }

    }