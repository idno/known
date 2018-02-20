<?php

    /**
     * All idno plugins should extend this component.
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Common {

        class Plugin extends Component
        {
            private $manifest;

            function init()
            {
                $result = parent::init();
                $this->registerContentTypes();
                \Idno\Core\Bonita\Main::additionalPath(dirname($this->getFilename()));

                return $result;
            }

            /**
             * Automatically registers content types associated with plugins,
             * as long as they're called IdnoPlugins\PLUGIN-NAME\ContentType
             */
            function registerContentTypes()
            {
                $namespace = $this->getNamespace();
                if (class_exists($namespace . '\\ContentType')) {
                    if (is_subclass_of($namespace . '\\ContentType', 'Idno\\Common\\ContentType')) {
                        \Idno\Common\ContentType::register($namespace . '\\ContentType');
                    }
                }
            }

            /**
             * Returns the bytes used by this plugin; if a user ID is included, limits to that user's uploads
             * @param string|bool $user (By default this isn't set)
             * @return int
             */
            function getFileUsage($user = false)
            {
                return 0;
            }

            /**
             * Parse and return the manifest.
             */
            public function getManifest() 
            {
                if (!empty($this->manifest))
                    return $this->manifest;
                
                $reflection = new \ReflectionClass(get_called_class());

                $definitionPath = $reflection->getFileName();
                
                $this->manifest = parse_ini_file(dirname($definitionPath) . '/plugin.ini');
                
                return $this->manifest;
            }
            
            /**
             * Return the version of this plugin.
             * @return type
             * @throws \Idno\Exceptions\ConfigurationException
             */
            public function getVersion() 
            {
                $manifest = $this->getManifest();
                
                if (empty($manifest['version']))
                    throw new \Idno\Exceptions\ConfigurationException(\Idno\Core\Idno::site()->language()->_('Plugin %s doesn\'t have a version', [get_class($this)]));
                
                return $manifest['version'];
            }
        }

    }