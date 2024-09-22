<?php

/**
 * Page handler class
 *
 * @package    idno
 * @subpackage core
 */

namespace Idno\Core {

    /**
     * Routing class.
     */
    class PageHandler implements \ArrayAccess, \Iterator
    {

        private $routes = [];
        private $public_routes = [];

        function routeTokens()
        {
            return array(
                ':string' => '([a-zA-Z]+)',
                ':number' => '([0-9]+)',
                ':alpha'  => '([a-zA-Z0-9-_]+)',
                ':id'     => '([A-Za-z0-9\-]+)'
            );
        }

    public static function serve($routes)
    {
        Hook::fire('before_request', compact('routes'));

        $request_method = strtolower(Idno::site()->request()->getMethod());
        $path_info = '/';

        if (! empty(Idno::site()->request()->getPathInfo())) {
            $path_info = Idno::site()->request()->getPathInfo();
        } 


        
        $discovered_handler = null;
        $regex_matches = array();

        if (isset($routes[$path_info])) {
            $discovered_handler = $routes[$path_info];
        } elseif ($routes) {
            $tokens = array(
                ':string' => '([a-zA-Z]+)',
                ':number' => '([0-9]+)',
                ':alpha'  => '([a-zA-Z0-9-_]+)'
            );
            foreach ($routes as $pattern => $handler_name) {
                $pattern = strtr($pattern, $tokens);
                if (preg_match('#^/?' . $pattern . '/?$#', $path_info, $matches)) {
                    $discovered_handler = $handler_name;
                    $regex_matches = $matches;
                    break;
                }
            }
        }


        $result = null;
        $handler_instance = null;

        if ($discovered_handler) {
            if (is_string($discovered_handler)) {
                $handler_instance = new $discovered_handler();
            } elseif (is_callable($discovered_handler)) {
                $handler_instance = $discovered_handler();
            }
        }

        if ($handler_instance) {
            unset($regex_matches[0]);


            if (method_exists($handler_instance, $request_method)) {
                Hook::fire('before_handler', compact('routes', 'discovered_handler', 'request_method', 'regex_matches'));
                $result = call_user_func_array(array($handler_instance, $request_method), $regex_matches);
                Hook::fire('after_handler', compact('routes', 'discovered_handler', 'request_method', 'regex_matches', 'result'));
            } else {
                Hook::fire('404', compact('routes', 'discovered_handler', 'request_method', 'regex_matches'));
            }
        } else {
            Hook::fire('404', compact('routes', 'discovered_handler', 'request_method', 'regex_matches'));
        }

        Hook::fire('after_request', compact('routes', 'discovered_handler', 'request_method', 'regex_matches', 'result'));
        \Idno\Core\Idno::site()->sendResponse();
    }

        /**
         * Registers a page handler for a given pattern, using Toro
         * page handling syntax
         *
         * @param string $pattern The pattern to match
         * @param string $handler The name of the Page class that will serve this route
         * @param bool   $public  If set to true, this page is always public, even on non-public sites
         */
        function addRoute(string $pattern, string $handler, bool $public = false)
        {
            if (defined('KNOWN_SUBDIRECTORY')) {
                if (substr($pattern, 0, 1) != '/') {
                    $pattern = '/' . $pattern;
                }
                $pattern = '/' . KNOWN_SUBDIRECTORY . $pattern;
            }
            $pattern = strtr($pattern, $this->routeTokens());
            if (class_exists($handler)) {
                $this->routes[$pattern] = $handler;
                if ($public == true) {
                    $this->public_routes[] = $handler;
                }
            } else {
                \Idno\Core\Idno::site()->logging()->error("Could not add $pattern. $handler not found");
            }
        }

        /**
         * Registers a page handler for a given pattern, using Toro
         * page handling syntax - and ensures it will be handled first
         *
         * @param string $pattern The pattern to match
         * @param string $handler The name of the Page class that will serve this route
         * @param bool   $public  If set to true, this page is always public, even on non-public sites
         */
        function hijackRoute(string $pattern, string $handler, bool $public = false)
        {
            $pattern = strtr($pattern, $this->routeTokens());
            if (class_exists($handler)) {
                unset($this->routes[$pattern]);
                unset($this->public_routes[$pattern]);
                $this->routes = array($pattern => $handler) + $this->routes;
                if ($public == true) {
                    $this->public_routes = array($pattern => $handler) + $this->public_routes;
                }
            }
        }

        /**
         * Mark a page handler class as offering public content even on walled garden sites
         *
         * @param $class
         */
        function addPublicRoute(string $class)
        {
            if (class_exists($class)) {
                $this->public_routes[] = $class;
            }
        }

        /**
         * Retrieve an array of walled garden page handlers
         *
         * @return array
         */
        function getPublicRoute()
        {
            if (!empty($this->public_routes)) {
                return $this->public_routes;
            }

            return array();
        }

        /**
         * Does the specified page handler class represent a public page, even on walled gardens?
         *
         * @param  $class
         * @return bool
         */
        function isRoutePublic(string $class) : bool
        {
            if (!empty($class)) {
                if (in_array($class, $this->getPublicRoute())) {
                    return true;
                }
                if ($class[0] != "\\") {
                    $class = "\\" . $class;
                    if (in_array($class, $this->getPublicRoute())) {
                        return true;
                    }
                }
            }

            return false;
        }

        /**
         * Retrieves an instantiated version of the page handler class responsible for
         * a particular page (if any). May also be a whole URL.
         *
         * @param  string $path_info The path, including the initial /, or the URL
         * @return bool|\Idno\Common\Page
         */
        function getRoute(string $path_info)
        {
            $path_info = parse_url($path_info, PHP_URL_PATH);
            if ($q = strpos($path_info, '?')) {
                $path_info = substr($path_info, 0, $q);
            }
            $discovered_handler = false;
            $matches = array();
            foreach ($this->routes as $pattern => $handler_name) {
                $pattern = strtr($pattern, $this->routeTokens());
                if (preg_match('#^/?' . $pattern . '/?$#', $path_info, $matches)) {
                    $discovered_handler = $handler_name;
                    $regex_matches = $matches;
                    break;
                }
            }
            if (class_exists($discovered_handler)) {
                $page = new $discovered_handler();
                if ($page instanceof \Idno\Common\Page) {
                    unset($matches[0]);
                    $page->arguments = array_values($matches);

                    return $page;
                }
            }

            return false;
        }

        /**
         * Adds a hook. Maps to ToroHook::add
         *
         * @see ToroHook
         *
         * @param string   $hookName Name of hook
         * @param callable $callable
         */
        static function hook(string $hookName, callable $callable)
        {
            \ToroHook::add($hookName, $callable);
        }

        // ArrayAccess & Iterator interfaces ////////

        public function offsetExists($offset): bool
        {
            return isset($this->routes[$offset]);
        }

        public function offsetGet($offset): mixed
        {
            return isset($this->routes[$offset]) ? $this->routes[$offset] : null;
        }

        public function offsetSet($offset, $value): void
        {
            if (is_null($offset)) {
                $this->routes[] = $value;
            } else {
                $this->routes[$offset] = $value;
            }
        }

        public function offsetUnset($offset): void
        {
            unset($this->routes[$offset]);
        }

        function rewind(): void
        {
            reset($this->routes);
        }

        function current(): mixed
        {
            return current($this->routes);
        }

        function key(): mixed
        {
            return key($this->routes);
        }

        function next(): void
        {
            next($this->routes);
        }

        function valid(): bool
        {
            return key($this->routes) !== null;
        }

        //////////////////////////////////////////////

    }

}

