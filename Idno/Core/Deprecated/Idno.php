<?php

namespace Idno\Core\Deprecated {

    /**
     * Deprecated functions from Idno
     */
    trait Idno
    {

        /**
         * Registers a page handler for a given pattern, using Toro
         * page handling syntax
         *
         * @deprecated
         * @param string $pattern The pattern to match
         * @param string $handler The name of the Page class that will serve this route
         * @param bool $public If set to true, this page is always public, even on non-public sites
         */
        function addPageHandler($pattern, $handler, $public = false)
        {
            \Idno\Core\Idno::site()->logging()->debug("DEPRECATION WARNING: Use \Idno\Core\Idno::site()->routes()->addRoute()");

            return \Idno\Core\Idno::site()->routes()->addRoute($pattern, $handler, $public);
        }

        /**
         * Registers a page handler for a given pattern, using Toro
         * page handling syntax - and ensures it will be handled first
         *
         * @deprecated
         * @param string $pattern The pattern to match
         * @param string $handler The name of the Page class that will serve this route
         * @param bool $public If set to true, this page is always public, even on non-public sites
         */
        function hijackPageHandler($pattern, $handler, $public = false)
        {
            \Idno\Core\Idno::site()->logging()->debug("DEPRECATION WARNING: Use \Idno\Core\Idno::site()->routes()->hijackRoute()");

            return \Idno\Core\Idno::site()->routes()->hijackRoute($pattern, $handler, $public);
        }

        /**
         * Mark a page handler class as offering public content even on walled garden sites
         * @deprecated
         * @param $class
         */
        function addPublicPageHandler($class)
        {
            \Idno\Core\Idno::site()->logging()->debug("DEPRECATION WARNING: Use \Idno\Core\Idno::site()->routes()->addPublicRoute()");

            return \Idno\Core\Idno::site()->routes()->addPublicRoute($class);
        }

        /**
         * Retrieve an array of walled garden page handlers
         * @deprecated
         * @return array
         */
        function getPublicPageHandlers()
        {
            \Idno\Core\Idno::site()->logging()->debug("DEPRECATION WARNING: Use \Idno\Core\Idno::site()->routes()->getPublicRoute()");

            return \Idno\Core\Idno::site()->routes()->getPublicRoute();
        }

        /**
         * Does the specified page handler class represent a public page, even on walled gardens?
         * @deprecated
         * @param $class
         * @return bool
         */
        function isPageHandlerPublic($class)
        {
            \Idno\Core\Idno::site()->logging()->debug("DEPRECATION WARNING: Use \Idno\Core\Idno::site()->routes()->isRoutePublic()");

            return \Idno\Core\Idno::site()->routes()->isRoutePublic($class);
        }

        /**
         * Retrieves an instantiated version of the page handler class responsible for
         * a particular page (if any). May also be a whole URL.
         *
         * @deprecated
         * @param string $path_info The path, including the initial /, or the URL
         * @return bool|\Idno\Common\Page
         */
        function getPageHandler($path_info)
        {
            \Idno\Core\Idno::site()->logging()->debug("DEPRECATION WARNING: Use \Idno\Core\Idno::site()->routes()->getRoute()");

            return \Idno\Core\Idno::site()->routes()->getRoute($path_info);
        }


        /**
         * Shortcut to trigger an event: supply the event name and
         * (optionally) an array of data, and get a variable back.
         *
         * @param string $eventName The name of the event to trigger
         * @param array $data Data to pass to the event
         * @param mixed $default Default response (if not forwarding)
         * @deprecated
         * @return mixed
         */

        function triggerEvent($eventName, $data = array(), $default = true)
        {
            \Idno\Core\Idno::site()->logging()->debug("DEPRECATION WARNING: Use \Idno\Core\Idno::site()->events()->triggerEvent()");

            return \Idno\Core\Idno::site()->events()->triggerEvent($eventName, $data, $default);
        }

        /**
         * Tells the system that callable $listener wants to be notified when
         * event $event is triggered. $priority is an optional integer
         * that specifies order priority; the higher the number, the earlier
         * in the chain $listener will be notified.
         *
         * @param string $event
         * @param callable $listener
         * @param int $priority
         * @deprecated
         */
        function addEventHook($event, $listener, $priority = 0)
        {
            \Idno\Core\Idno::site()->logging()->debug("DEPRECATION WARNING: Use \Idno\Core\Idno::site()->events()->addListener()");

            return \Idno\Core\Idno::site()->events()->addListener($event, $listener, $priority);
        }
    }
}
