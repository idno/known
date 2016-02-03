<?php

    /**
     * Lazy analogue to array_map. Applies a function to each value
     * from an iterator.
     */

    namespace Idno\Common {

        class MappingIterator extends \IteratorIterator
        {

            private $func;

            /**
             * @param Traversable $iterator
             * @param callable $func
             */
            function __construct($iterator, $func)
            {
                parent::__construct($iterator);
                $this->func = $func;
            }

            function current()
            {
                return call_user_func($this->func, parent::current());
            }

        }
    }
