<?php

/**
 * Service Container for dependency management
 * 
 * Replaces the god object pattern in Idno.php with proper dependency injection
 *
 * @package    idno
 * @subpackage core
 */

namespace Idno\Core {

    class ServiceContainer
    {
        private $services = [];
        private $singletons = [];
        private $factories = [];

        /**
         * Register a service factory
         */
        public function register(string $name, callable $factory, bool $singleton = true): void
        {
            $this->factories[$name] = $factory;
            if ($singleton) {
                $this->singletons[$name] = true;
            }
        }

        /**
         * Get a service instance
         */
        public function get(string $name)
        {
            // Return cached singleton if available
            if (isset($this->services[$name])) {
                return $this->services[$name];
            }

            // Create new instance using factory
            if (isset($this->factories[$name])) {
                $service = $this->factories[$name]($this);
                
                // Cache if singleton
                if (isset($this->singletons[$name])) {
                    $this->services[$name] = $service;
                }
                
                return $service;
            }

            throw new \InvalidArgumentException("Service '{$name}' not registered");
        }

        /**
         * Check if service is registered
         */
        public function has(string $name): bool
        {
            return isset($this->factories[$name]);
        }

        /**
         * Register core services
         */
        public function registerCoreServices(Config $config): void
        {
            // Database service
            $this->register('database', function() use ($config) {
                switch (trim(strtolower($config->database))) {
                    case 'mongo':
                    case 'mongodb':
                        return new \Idno\Data\Mongo();
                    case 'mysql':
                        return new \Idno\Data\MySQL();
                    case 'beanstalk-mysql':
                        $config->dbhost = $_SERVER['RDS_HOSTNAME'];
                        $config->dbuser = $_SERVER['RDS_USERNAME'];
                        $config->dbpass = $_SERVER['RDS_PASSWORD'];
                        $config->dbport = $_SERVER['RDS_PORT'];
                        if (empty($config->dbname)) {
                            $config->dbname = $_SERVER['RDS_DB_NAME'];
                        }
                        return new \Idno\Data\MySQL();
                    default:
                        return $this->createComponent(
                            $config->database,
                            "Idno\\Core\\DataConcierge",
                            "Idno\\Data\\",
                            "Idno\\Data\\MySQL"
                        );
                }
            });

            // Filesystem service
            $this->register('filesystem', function() use ($config) {
                switch ($config->filesystem) {
                    case 'local':
                        return new \Idno\Files\LocalFileSystem();
                    default:
                        $filesystem = $this->get('database')->getFilesystem();
                        if (!empty($config->filesystem) && empty($filesystem)) {
                            return $this->createComponent(
                                $config->filesystem,
                                "Idno\\Files\\FileSystem",
                                "Idno\\Files\\",
                                "Idno\\Files\\LocalFileSystem"
                            );
                        }
                        return $filesystem ?: new \Idno\Files\LocalFileSystem();
                }
            });

            // Template service
            $this->register('template', function() use ($config) {
                return $this->createComponent(
                    $config->template,
                    Template::class,
                    "Idno\\Core\\",
                    HybridTwigTemplate::class
                );
            });

            // Session service
            $this->register('session', function() {
                return new Session();
            });

            // Event dispatcher
            $this->register('events', function() {
                return new EventDispatcher();
            });

            // Logging service
            $this->register('logging', function() use ($config) {
                $logging = new Logging();
                if (isset($config->loglevel)) {
                    $logging->setLogLevel($config->loglevel);
                }
                return $logging;
            });

            // Cache service
            $this->register('cache', function() use ($config) {
                $default = "Idno\\Caching\\FilesystemCache";
                if (extension_loaded('apc') && ini_get('apc.enabled')) {
                    $default = "Idno\\Caching\\APCuCache";
                }
                return $this->createComponent(
                    $config->cache,
                    "Idno\\Caching\\Cache",
                    "Idno\\Caching\\",
                    $default
                );
            });

            // Queue service
            $this->register('queue', function() use ($config) {
                return $this->createComponent(
                    $config->event_queue,
                    "Idno\\Core\\EventQueue",
                    "Idno\\Core\\",
                    "Idno\\Core\\SynchronousQueue"
                );
            });

            // Statistics service
            $this->register('statistics', function() use ($config) {
                return $this->createComponent(
                    $config->statistics_collector,
                    "Idno\\Stats\\StatisticsCollector",
                    "Idno\\Stats\\",
                    "Idno\\Stats\\DummyStatisticsCollector"
                );
            });

            // Other core services
            $this->register('actions', function() {
                return new Actions();
            });

            $this->register('language', function() {
                $language = new Language();
                $language->register(new GetTextTranslation());
                return $language;
            });

            $this->register('syndication', function() {
                return new Syndication();
            });

            $this->register('reader', function() {
                return new Reader();
            });

            $this->register('helper_robot', function() {
                return new HelperRobot();
            });

            $this->register('routes', function() {
                return new PageHandler();
            });
        }

        /**
         * Helper method to create components with fallbacks
         */
        private function createComponent($className, $expectedBaseClass, $defaultClassNameBase, $defaultClass)
        {
            // Try full namespace
            if (!empty($className) && class_exists($className)) {
                if (is_subclass_of($className, $expectedBaseClass)) {
                    return new $className();
                }
            }

            // Try base class creation
            $fullClassName = $defaultClassNameBase . $className;
            if (class_exists($fullClassName) && is_subclass_of($fullClassName, $expectedBaseClass)) {
                return new $fullClassName();
            }

            // Use default
            if (!empty($defaultClass)) {
                return new $defaultClass();
            }

            throw new \RuntimeException("Could not create component: {$className}");
        }
    }
}