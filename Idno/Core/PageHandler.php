<?php

    /**
     * Page handler class
     *
     * @package idno
     * @subpackage core
     */

namespace Idno\Core {

    class PageHandler extends \Toro
    {

        /**
         * Adds a hook. Maps to ToroHook::add
         *
         * @see ToroHook
         *
         * @param string $hookName Name of hook
         * @param callable $callable
         */
        static function hook($hookName, $callable)
        {
            \ToroHook::add($hookName, $callable);
        }

    }

}

