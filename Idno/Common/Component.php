<?php

    /**
     * All idno components inherit this base class
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Common {

        class Component
        {

            function __construct()
            {
                $this->init();
                $this->registerEventHooks();
                $this->registerPages();
            }

            /**
             * Any initialization tasks to perform? This is the place to
             * do it. Note that any page registration tasks should be
             * performed using registerPages(), and any event hooks should
             * be performed using registerEventHooks().
             */
            function init()
            {
            }

            /**
             * Here's your handy-dandy placeholder for registering any
             * event hooks with the EventDispatcher.
             *
             * Note that misc init functionality should be placed in the
             * init() function, and page routing / models should be placed
             * in registerPages().
             */
            function registerEventHooks()
            {
            }

            /**
             * Registers any pages with the router. If components don't
             * extend this function, no pages are registered. It's up to
             * the components to either use a separate function to actually
             * define the page action, or to use an anonymous function.
             * Choices, people!
             */
            function registerPages()
            {
            }

            /**
             * Helper function that gets the full class name of this entity
             * @return string
             */
            function getClass()
            {
                return get_class($this);
            }

            /**
             * Helper method to retrieve the filename of the current component
             * (works with inheritance).
             * @return string
             */
            function getFilename()
            {
                $reflector = new \ReflectionClass(get_class($this));

                return $reflector->getFileName();
            }

            /**
             * Returns a camelCase version of the object title, suitable for use in element IDs
             * @return string
             */
            function getIDSelector()
            {
                return $this->camelCase($this->getTitle());
            }

            /**
             * Returns the camelCased version of a given string
             * @param $string
             * @return $string
             */
            function camelCase($string)
            {
                $string = preg_replace_callback('/\s([a-z])/', function ($matches) {
                    return strtoupper($matches[0]);
                }, strtolower($string));
                $string = preg_replace('/\s/', '', $string);

                return $string;
            }

            /**
             * Returns a camelCase version of the object class, suitable for use in element IDs
             * @return string
             */
            function getClassSelector()
            {
                return $this->camelCase($this->getClassName());
            }

            /**
             * Get the name of this class without its namespace
             * @return string
             */
            function getClassName()
            {
                return str_replace('\\', '', str_replace($this->getNamespace(), '', get_class($this)));
            }

            /**
             * A helper method that retrieves the current namespace of this class
             * (eg, the namespace of a child class).
             */
            function getNamespace()
            {
                $reflector = new \ReflectionClass(get_class($this));

                return $reflector->getNamespaceName();
            }

            /**
             * Gets the name of this class including its namespace
             * @param bool $convert_slashes If set to true, converts \ slashes to / (false by default)
             * @return string
             */
            function getFullClassName($convert_slashes = false)
            {
                $return = get_class($this);
                if ($convert_slashes) {
                    $return = str_replace('\\', '/', $return);
                }

                return $return;
            }

        }

    }