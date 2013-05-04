<?php

    /**
     * Plugin management class
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Core {

        class Plugins extends \Idno\Common\Component {

            public $plugins = array();  // Property containing instantiated plugin classes

            /**
             * On initialization, the plugin management class loads plugins from the system
             * configuration and saves an instantiated copy of each one in its local $plugins
             * array property.
             */
            public function init() {

                if (!empty(site()->config()->plugins)) {
                    foreach(site()->config()->plugins as $plugin) {
                        if (is_subclass_of("IdnoPlugins\\{$plugin}\\Main", 'Idno\\Common\\Plugin')) {
                            $class = "IdnoPlugins\\{$plugin}\\Main";
                            $this->plugins[$plugin] = new $class();
                        }
                    }
                }

            }

            /**
             * Magic method to return the instantiated plugin object when
             * $Plugins->plugin_name is accessed
             * @param $name
             * @return mixed
             */
            public function __get($name) {
                if (!empty($this->plugins[$name])) return $this->plugins[$name];
                return null;
            }

            /**
             * Magic method to check for the existence of plugin objects as properties
             * on the plugin handler object
             * @param $name
             * @return bool
             */
            public function __isset($name) {
                if (!empty($this->plugins[$name])) return true;
                return false;
            }

            /**
             * Retrieves the array of loaded plugin objects
             * @return array
             */
            public function getLoaded() {
                return $this->plugins;
            }

        }

    }