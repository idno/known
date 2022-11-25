<?php

namespace Idno\Caching {

    use Symfony\Component\Cache\Adapter\AbstractAdapter;

    abstract class Cache
        extends \Idno\Common\Component
        implements \ArrayAccess
    {
        /// This is the cache engine underlaying the engine
        private $cache;

        /**
         * Set the cache engine used by this.
         *
         * @param AbstractAdapter $adapter
         */
        protected function setCacheEngine(AbstractAdapter $adapter)
        {
            $this->cache = $adapter;
        }

        /**
         * Get the current cache engine.
         *
         * @return AbstractAdapter
         */
        public function getCacheEngine() : AbstractAdapter
        {
            return $this->cache;
        }


        /**
         * Return the number of keys currently stored.
         *
         * @deprecated
         */
        public function size()
        {

            $engine = $this->getCacheEngine();

            return count($engine->getItems());
        }


        /**
         * Retrieve a value from the store.
         *
         * @param  $key Key to retrieve
         * @return mixed|false
         */
        public function load($key)
        {

            $engine = $this->getCacheEngine();

            $item = $engine->getItem($key);
            if ($item->isHit()) {
                return $item->get();
            }

            return false;
        }

        /**
         * Store or replace a value in the cache.
         *
         * @param  $key   string Identifier for this value
         * @param  $value mixed Value to store
         * @return bool
         */
        public function store($key, $value)
        {

            $engine = $this->getCacheEngine();

            $item = $engine->getItem($key);
            $item->set($value);

            return $engine->save($item);
        }

        /**
         * Remove a key from the cache.
         *
         * @param  The key
         * @return bool
         */
        public function delete($key)
        {

            $engine = $this->getCacheEngine();

            return $engine->delete($key);
        }

        /* Object interface */

        public function __isset($key)
        {
            return (bool)$this->load($key);
        }

        public function __unset($key)
        {
            return $this->delete($key);
        }

        /* Candy */

        public function __get($key)
        {
            return $this->load($key);
        }

        public function __set($key, $value)
        {
            return $this->store($key, $value);
        }


        /* Array access interface */

        public function offsetGet($key)
        {
            return $this->load($key);
        }

        public function offsetSet(mixed $key, mixed $value): void
        {
            $this->store($key, $value);
        }

        public function offsetExists(mixed $key): bool
        {
            return (bool)$this->load($key);
        }

        public function offsetUnset(mixed $key): void
        {
            $this->delete($key);
        }
    }

}
