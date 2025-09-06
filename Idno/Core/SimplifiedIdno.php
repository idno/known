<?php

/**
 * Simplified Idno Core Class
 * 
 * Demonstrates how the main Idno class could be refactored using dependency injection
 * and the ServiceContainer pattern to reduce complexity
 *
 * @package    idno
 * @subpackage core
 */

namespace Idno\Core {

    use Idno\Common\Page;
    use Idno\Entities\User;

    class SimplifiedIdno extends \Idno\Common\Component
    {
        private ServiceContainer $container;
        private Config $config;
        private static ?SimplifiedIdno $instance = null;
        private ?Page $currentPage = null;

        public function __construct()
        {
            parent::__construct();
            $this->container = new ServiceContainer();
            $this->config = new Config();
        }

        /**
         * Initialize the application
         */
        public function init(): void
        {
            self::$instance = $this;

            // Check if this is a default configuration (redirect to installer)
            if ($this->config->isDefaultConfig()) {
                header('Location: ./warmup/');
                exit;
            }

            // Load configuration
            $this->config->loadIniFiles();
            $this->config->load();

            // Register core services with the container
            $this->container->registerCoreServices($this->config);

            // Register pages and plugins
            $this->registerPages();
            
            // Initialize plugins and themes
            $this->container->register('plugins', function() {
                return new Plugins();
            });
            
            $this->container->register('themes', function() {
                return new Themes();
            });

            // Authenticate user after all components are ready
            $this->session()->tryAuthUser();
            $this->upgrade();

            // Register user events
            User::registerEvents();
        }

        /**
         * Register core page routes
         */
        public function registerPages(): void
        {
            $routes = $this->routes();
            $permalinkRoute = \Idno\Common\Entity::getPermalinkRoute();

            // Homepage and feeds
            $routes->addRoute('/?', '\Idno\Pages\Homepage');
            $routes->addRoute('/feed\.xml', '\Idno\Pages\Feed');
            $routes->addRoute('/feed/?', '\Idno\Pages\Feed');
            $routes->addRoute('/rss\.xml', '\Idno\Pages\Feed');

            // Entity routes
            $routes->addRoute('/view/:id/?', '\Idno\Pages\Entity\View');
            $routes->addRoute('/s/:id/?', '\Idno\Pages\Entity\Shortlink');
            $routes->addRoute($permalinkRoute . '/?', '\Idno\Pages\Entity\View');
            $routes->addRoute('/edit/:id/?', '\Idno\Pages\Entity\Edit');
            $routes->addRoute('/delete/:id/?', '\Idno\Pages\Entity\Delete');

            // User routes
            $routes->addRoute('/profile/([^\/]+)/?', '\Idno\Pages\User\View');
            $routes->addRoute('/profile/([^\/]+)/edit/?', '\Idno\Pages\User\Edit');

            // Search routes
            $routes->addRoute('/search/?', '\Idno\Pages\Search\Forward');
            $routes->addRoute('/tag/([^\s]+)\/?', '\Idno\Pages\Search\Tags');

            // File routes
            $routes->addRoute('/file/upload/?', '\Idno\Pages\File\Upload', true);
            $routes->addRoute('/file/(:id)(/.*)?', '\Idno\Pages\File\View', true);

            // Service routes
            $routes->addRoute('/service/db/optimise/?', '\Idno\Pages\Service\Db\Optimise');
            $routes->addRoute('/service/security/csrftoken/?', '\Idno\Pages\Service\Security\CSRFToken');
        }

        /**
         * Get service from container
         */
        public function get(string $serviceName)
        {
            return $this->container->get($serviceName);
        }

        /**
         * Service accessors with proper typing
         */
        public function db(): ?DataConcierge
        {
            return $this->get('database');
        }

        public function events(): ?EventDispatcher
        {
            return $this->get('events');
        }

        public function queue(): ?EventQueue
        {
            return $this->get('queue');
        }

        public function filesystem(): ?\Idno\Files\FileSystem
        {
            return $this->get('filesystem');
        }

        public function logging(): ?Logging
        {
            return $this->get('logging');
        }

        public function cache(): ?\Idno\Caching\PersistentCache
        {
            return $this->get('cache');
        }

        public function statistics(): ?\Idno\Stats\StatisticsCollector
        {
            return $this->get('statistics');
        }

        public function routes(): ?PageHandler
        {
            return $this->get('routes');
        }

        public function config(?string $setting = null)
        {
            if ($setting === null) {
                return $this->config;
            }
            return $this->config->$setting;
        }

        public function syndication(): ?Syndication
        {
            return $this->get('syndication');
        }

        public function session(): ?Session
        {
            return $this->get('session');
        }

        public function plugins(): ?Plugins
        {
            return $this->get('plugins');
        }

        public function themes(): ?Themes
        {
            return $this->get('themes');
        }

        public function template(): ?Template
        {
            return $this->get('template');
        }

        public function language(): ?Language
        {
            return $this->get('language');
        }

        public function actions(): ?Actions
        {
            return $this->get('actions');
        }

        public function reader(): ?Reader
        {
            return $this->get('reader');
        }

        /**
         * Page management
         */
        public function setCurrentPage(Page $page): void
        {
            $this->currentPage = $page;
        }

        public function currentPage(): ?Page
        {
            return $this->currentPage;
        }

        /**
         * Permission checks
         */
        public function canEdit(string $userId = ''): bool
        {
            if (!$this->session()->isLoggedOn()) {
                return false;
            }

            $userId = $userId ?: $this->session()->currentUserUUID();
            $user = \Idno\Entities\User::getByUUID($userId);

            if (!$user) {
                return false;
            }

            return $this->events()->triggerEvent(
                'canEdit/site',
                [
                    'object' => $this,
                    'user_id' => $userId,
                    'user' => $user
                ],
                $user->isAdmin()
            );
        }

        public function canWrite(string $userId = ''): bool
        {
            if (!$this->session()->isLoggedOn()) {
                return false;
            }

            $userId = $userId ?: $this->session()->currentUserUUID();
            $user = \Idno\Entities\User::getByUUID($userId);

            if (!$user) {
                return false;
            }

            return $this->events()->triggerEvent(
                'canWrite/site',
                [
                    'object' => $this,
                    'user_id' => $userId,
                    'user' => $user
                ],
                !$user instanceof \Idno\Entities\RemoteUser && empty($user->read_only)
            );
        }

        public function canRead(string $userId = ''): bool
        {
            return true; // Public by default, can be extended via events
        }

        /**
         * Application lifecycle
         */
        public function upgrade(): void
        {
            $lastUpdate = $this->config()->update_version ?? 0;
            $currentVersion = Version::build();

            if ($lastUpdate < $currentVersion) {
                $success = $this->events()->triggerEvent('upgrade', [
                    'last_update' => $lastUpdate,
                    'new_version' => $currentVersion
                ]);

                if ($success) {
                    $this->config()->update_version = $currentVersion;
                    $this->config()->save();
                    $this->logging()->info("Known upgraded from $lastUpdate to $currentVersion");
                } else {
                    $this->logging()->error("There was a problem applying an update.");
                }
            }
        }

        /**
         * Site icons
         */
        public function getSiteIcons(): array
        {
            $icons = [
                'defaults' => [
                    'default' => $this->config()->getStaticURL() . 'gfx/logos/logo_k.png',
                    'default_16' => $this->config()->getStaticURL() . 'gfx/logos/logo_k_16.png',
                    'default_32' => $this->config()->getStaticURL() . 'gfx/logos/logo_k_32.png',
                    'default_192' => $this->config()->getStaticURL() . 'gfx/logos/logo_k_192.png',
                ]
            ];

            // Add page-specific icons if available
            if ($this->currentPage && $pageIcons = $this->currentPage->getIcon()) {
                $icons['page'] = $pageIcons;
            }

            return $this->events()->triggerEvent('site/icons', ['object' => $this], $icons);
        }

        /**
         * Static instance accessor
         */
        public static function site(): ?SimplifiedIdno
        {
            return self::$instance;
        }

        /**
         * Get current version
         */
        public function getVersion(): string
        {
            return Version::version();
        }

        /**
         * Prevent serialization of stateful object
         */
        public function __sleep(): array
        {
            return [];
        }
    }

    /**
     * Helper function for backward compatibility
     */
    function site(): ?SimplifiedIdno
    {
        return SimplifiedIdno::site();
    }
}