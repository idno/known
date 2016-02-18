<?php

    /**
     * Plugin management class
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Core {

        use Idno\Common\Plugin;

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

                // Add a classloader to look for a package autoloader
                // TODO: make sure this works with multitenant sites and plugins on an external path
                spl_autoload_register(function ($class) {
                    foreach (\Idno\Core\Idno::site()->config->config['plugins'] as $plugin) {
                        if (file_exists(\Idno\Core\Idno::site()->config()->path . '/IdnoPlugins/' . $plugin . '/autoloader.php')) {
                            include_once(\Idno\Core\Idno::site()->config()->path . '/IdnoPlugins/' . $plugin . '/autoloader.php');
                        }
                    }
                });

                if (!empty(\Idno\Core\Idno::site()->config()->directloadplugins)) {
                    foreach (\Idno\Core\Idno::site()->config()->directloadplugins as $plugin => $folder) {
                        @include $folder . '/Main.php';
                    }
                }

                if (!empty(\Idno\Core\Idno::site()->config()->prerequisiteplugins)) {
                    \Idno\Core\Idno::site()->config()->prerequisiteplugins = array_unique(\Idno\Core\Idno::site()->config()->prerequisiteplugins);
                    foreach (\Idno\Core\Idno::site()->config()->prerequisiteplugins as $plugin) {
                        if (is_subclass_of("IdnoPlugins\\{$plugin}\\Main", 'Idno\\Common\\Plugin')) {
                            $class = "IdnoPlugins\\{$plugin}\\Main";
                            if (empty($this->plugins[$plugin])) {
                                $this->plugins[$plugin] = new $class();
                            }
                        }
                    }
                }
                if (!empty(\Idno\Core\Idno::site()->config()->alwaysplugins)) {
                    \Idno\Core\Idno::site()->config->plugins = array_merge(\Idno\Core\Idno::site()->config->plugins, \Idno\Core\Idno::site()->config->alwaysplugins);
                }
                if (!empty(\Idno\Core\Idno::site()->config()->plugins)) {
                    \Idno\Core\Idno::site()->config->plugins = array_unique(\Idno\Core\Idno::site()->config->plugins);
                    foreach (\Idno\Core\Idno::site()->config()->plugins as $plugin) {
                        if (!in_array($plugin, \Idno\Core\Idno::site()->config()->antiplugins)) {
                            if (is_subclass_of("IdnoPlugins\\{$plugin}\\Main", 'Idno\\Common\\Plugin')) {
                                $class = "IdnoPlugins\\{$plugin}\\Main";
                                if (empty($this->plugins[$plugin])) {
                                    $this->plugins[$plugin] = new $class();
                                }
                            }
                        }
                    }
                }

                \Idno\Core\Idno::site()->triggerEvent('plugins/loaded');
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
             * Is the specified plugin allowed to be displayed?
             * @param $plugin
             * @return bool
             */
            public function isVisible($plugin)
            {
                if (empty(\Idno\Core\Idno::site()->config()->hiddenplugins)) {
                    return true;
                }
                if (!in_array($plugin, \Idno\Core\Idno::site()->config()->hiddenplugins)) {
                    return true;
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
                if ($folders = scandir(\Idno\Core\Idno::site()->config()->path . '/IdnoPlugins')) {
                    foreach ($folders as $folder) {
                        if ($folder != '.' && $folder != '..') {
                            if (is_dir(\Idno\Core\Idno::site()->config()->path . '/IdnoPlugins/' . $folder)) {
                                if (file_exists(\Idno\Core\Idno::site()->config()->path . '/IdnoPlugins/' . $folder . '/plugin.ini')) {
                                    if ($this->isAllowed($folder)) {
                                        $plugins[$folder] = parse_ini_file(\Idno\Core\Idno::site()->config()->path . '/IdnoPlugins/' . $folder . '/plugin.ini', true);
                                    }
                                }
                            }
                        }
                    }
                }
                if (defined('KNOWN_MULTITENANT_HOST')) {
                    $host = KNOWN_MULTITENANT_HOST;
                    if (file_exists(\Idno\Core\Idno::site()->config()->path . '/hosts/' . $host . '/IdnoPlugins')) {
                        if ($folders = scandir(\Idno\Core\Idno::site()->config()->path . '/hosts/' . $host . '/IdnoPlugins')) {
                            foreach ($folders as $folder) {
                                if ($folder != '.' && $folder != '..') {
                                    if (file_exists(\Idno\Core\Idno::site()->config()->path . '/hosts/' . $host . '/IdnoPlugins/' . $folder . '/plugin.ini')) {
                                        if ($this->isAllowed($folder)) {
                                            $plugins[$folder] = parse_ini_file(\Idno\Core\Idno::site()->config()->path . '/hosts/' . $host . '/IdnoPlugins/' . $folder . '/plugin.ini', true);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                ksort($plugins);

                return $plugins;
            }

            /**
             * Is the specified plugin allowed to be loaded?
             * @param $plugin
             * @return bool
             */
            public function isAllowed($plugin)
            {
                if (empty(\Idno\Core\Idno::site()->config()->antiplugins)) {
                    return true;
                }
                if (!in_array($plugin, \Idno\Core\Idno::site()->config()->antiplugins)) {
                    return true;
                }
                if (!empty(\Idno\Core\Idno::site()->config()->prerequisiteplugins)) {
                    if (in_array($plugin, \Idno\Core\Idno::site()->config()->prerequisiteplugins)) {
                        return true;
                    }
                }
                if (!empty(\Idno\Core\Idno::site()->config()->alwaysplugins)) {
                    if (in_array($plugin, \Idno\Core\Idno::site()->config()->alwaysplugins)) {
                        return true;
                    }
                }

                return false;
            }

            /**
             * Retrieves the number of bytes stored by all plugins in the system.
             * @return int
             */
            public function getTotalFileUsage()
            {
                if ($usage = $this->getFileUsageByPlugin()) {
                    return (int)array_sum($usage);
                }

                return 0;
            }

            /**
             * Retrieves the file bytes stored by each plugin
             * @return array
             */
            public function getFileUsageByPlugin()
            {
                $usage = [];
                if (!empty($this->plugins)) {
                    foreach ($this->plugins as $plugin) {
                        if ($plugin instanceof Plugin) {
                            $usage[$plugin->getClass()] = (int)$plugin->getFileUsage();
                        }
                    }
                }

                return $usage;
            }

        }

    }