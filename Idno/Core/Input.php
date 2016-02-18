<?php

    /**
     * Input handling methods
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Core {

        class Input extends \Idno\Common\Component
        {

            /**
             * Retrieves input from $_REQUEST, and performs optional filtering.
             *
             * @param string $name Name of the input variable
             * @param mixed $default A default return value if no value specified (default: null)
             * @param boolean $filter Whether or not to filter the variable for safety (default: null), you can pass
             *                 a callable method, function or enclosure with a definition like function($name, $value), which
             *                 will return the filtered result.
             * @return mixed
             */
            public static function getInput($name, $default = null, callable $filter = null)
            {
                if (!empty($name)) {
                    if (!empty($_REQUEST[$name])) {
                        $value = $_REQUEST[$name];
                    }
                    if ((empty($value)) && (!empty($default)))
                        $value = $default;
                    if (!empty($value)) {
                        if (isset($filter) && is_callable($filter)) {
                            $value = call_user_func($filter, $name, $value);
                        }

                        // TODO, we may want to add some sort of system wide default filter for when $filter is null

                        return $value;
                    }
                }

                return null;
            }

        }

    }