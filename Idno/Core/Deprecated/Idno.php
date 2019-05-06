<?php

namespace Idno\Core\Deprecated {
    
    /**
     * Deprecated functions from Idno
     */
    trait Idno {
        
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
            \Idno\Core\Idno::site()->logging()->warning("DEPRECATION WARNING: \Idno\Core\Idno::site()->routes()->addRoute()");
            
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
            \Idno\Core\Idno::site()->logging()->warning("DEPRECATION WARNING: \Idno\Core\Idno::site()->routes()->hijackRoute()");
            
            return \Idno\Core\Idno::site()->routes()->hijackRoute($pattern, $handler, $public);
        }

        /**
         * Mark a page handler class as offering public content even on walled garden sites
         * @deprecated
         * @param $class
         */
        function addPublicPageHandler($class)
        {
            \Idno\Core\Idno::site()->logging()->warning("DEPRECATION WARNING: \Idno\Core\Idno::site()->routes()->addPublicRoute()");
            
            return \Idno\Core\Idno::site()->routes()->addPublicRoute($class);
        }

        /**
         * Retrieve an array of walled garden page handlers
         * @deprecated
         * @return array
         */
        function getPublicPageHandlers()
        {
            \Idno\Core\Idno::site()->logging()->warning("DEPRECATION WARNING: \Idno\Core\Idno::site()->routes()->getPublicRoute()");
            
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
            \Idno\Core\Idno::site()->logging()->warning("DEPRECATION WARNING: \Idno\Core\Idno::site()->routes()->isRoutePublic()");
            
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
            \Idno\Core\Idno::site()->logging()->warning("DEPRECATION WARNING: \Idno\Core\Idno::site()->routes()->getRoute()");
            
            return \Idno\Core\Idno::site()->routes()->getRoute($path_info);
        }
    }
}