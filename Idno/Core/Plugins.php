<?php

    /**
     * Plugin management class
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Core {

        class Plugins extends \Idno\Common\Component
        {

            public $plugins = array(); // Property containing instantiated plugin classes

            /**
             * On initialization, the plugin management class loads plugins from the system
             * configuration and saves an instantiated copy of each one in its local $plugins
             * array property.
             */
            public function init()
            {

                if (!empty(site()->config()->alwaysplugins)) {
                    site()->config->plugins = array_merge(site()->config->plugins, site()->config->alwaysplugins);
                }
                if (!empty(site()->config()->plugins)) {
                    foreach (site()->config()->plugins as $plugin) {
                        if (!in_array($plugin, site()->config()->antiplugins)) {
                            if (is_subclass_of("IdnoPlugins\\{$plugin}\\Main", 'Idno\\Common\\Plugin')) {
                                $class                  = "IdnoPlugins\\{$plugin}\\Main";
                                if (empty($this->plugins[$plugin])) {
                                    $this->plugins[$plugin] = new $class();
                                }
                            }
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
            public function __get($name)
            {
                if (!empty($this->plugins[$name])) return $this->plugins[$name];

                return null;
            }

            /**
             * Magic method to check for the existence of plugin objects as properties
             * on the plugin handler object
             * @param $name
             * @return bool
             */
            public function __isset($name)
            {
                if (!empty($this->plugins[$name])) return true;

                return false;
            }

            /**
             * Retrieves the array of loaded plugin objects
             * @return array
             */
            public function getLoaded()
            {
                return $this->plugins;
            }

            /**
             * Retrieve the Plugin object associated with a loaded plugin
             * @param string $plugin Plugin name
             * @return bool|\Idno\Common\Plugin
             */
            public function get($plugin)
            {
                if (!empty($this->plugins[$plugin])) {
                    return $this->plugins[$plugin];
                }

                return false;
            }

            /**
             * Retrieves a list of stored plugins (but not necessarily loaded ones)
             * @return array
             */
            public function getStored()
            {
                $plugins = array();
                if ($folders = scandir(\Idno\Core\site()->config()->path . '/IdnoPlugins')) {
                    foreach ($folders as $folder) {
                        if ($folder != '.' && $folder != '..') {
                            if (file_exists(\Idno\Core\site()->config()->path . '/IdnoPlugins/' . $folder . '/plugin.ini')) {
                                $plugins[$folder] = parse_ini_file(\Idno\Core\site()->config()->path . '/IdnoPlugins/' . $folder . '/plugin.ini', true);
                            }
                        }
                    }
                }
                if (defined('KNOWN_MULTITENANT_HOST')) {
                    $host = KNOWN_MULTITENANT_HOST;
                    if (file_exists(\Idno\Core\site()->config()->path . '/hosts/' . $host . '/IdnoPlugins')) {
                        if ($folders = scandir(\Idno\Core\site()->config()->path . '/hosts/' . $host . '/IdnoPlugins')) {
                            foreach ($folders as $folder) {
                                if ($folder != '.' && $folder != '..') {
                                    if (file_exists(\Idno\Core\site()->config()->path . '/hosts/' . $host . '/IdnoPlugins/' . $folder . '/plugin.ini')) {
                                        $plugins[$folder] = parse_ini_file(\Idno\Core\site()->config()->path . '/hosts/' . $host . '/IdnoPlugins/' . $folder . '/plugin.ini', true);
                                    }
                                }
                            }
                        }
                    }
                }
                ksort($plugins);

                return $plugins;
            }

        }

    }