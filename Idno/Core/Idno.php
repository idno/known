<?php

    /**
     * Base Idno class
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Core {

        use Idno\Common\Page;
        use Idno\Entities\User;

        class Idno extends \Idno\Common\Component
        {

            public $db;
            public $filesystem;
            public $config;
            public $session;
            public $template;
            public $language;
            public $actions;
            public $plugins;
            public $dispatcher;
            public $queue;
            public $pagehandlers;
            public $public_pages;
            public $syndication;
            /* @var \Psr\Log\LoggerInterface $logging */
            public $logging;
            /* @var \Idno\Core\Idno $site */
            public static $site;
            public $currentPage;
            public $known_hub;
            public $helper_robot;
            public $reader;
            public $cache;
            public $statistics;

            function __construct()
            {
                parent::__construct();
                // auth the user after all the plugins and pages have registered so they can respond to events
                $this->session()->tryAuthUser();
                $this->upgrade();
            }

            function init()
            {
                self::$site       = $this;
                $this->dispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
                $this->config     = new Config();
                if ($this->config->isDefaultConfig()) {
                    header('Location: ./warmup/');
                    exit; // Load the installer
                }

                // We need to load initial values from the .ini files
                $this->config()->loadIniFiles();

                switch (trim(strtolower($this->config->database))) {
                    case 'mongo':
                    case 'mongodb':
                        $this->db = new \Idno\Data\Mongo();
                        break;
                    case 'mysql':
                        $this->db = new \Idno\Data\MySQL();
                        break;
                    case 'beanstalk-mysql': // A special instance of MYSQL designed for use with Amazon Elastic Beanstalk
                        $this->config->dbhost = $_SERVER['RDS_HOSTNAME'];
                        $this->config->dbuser = $_SERVER['RDS_USERNAME'];
                        $this->config->dbpass = $_SERVER['RDS_PASSWORD'];
                        $this->config->dbport = $_SERVER['RDS_PORT'];
                        if (empty($this->config->dbname)) {
                            $this->config->dbname = $_SERVER['RDS_DB_NAME'];
                        }
                        $this->db = new \Idno\Data\MySQL();
                        break;
                    default:
                        $this->db = $this->componentFactory($this->config->database, "Idno\\Core\\DataConcierge", "Idno\\Data\\", "Idno\\Data\\MySQL");
                        break;
                }

                switch ($this->config->filesystem) {
                    case 'local':
                        $this->filesystem = new \Idno\Files\LocalFileSystem();
                        break;
                    default:
                        if (class_exists("Idno\\Files\\{$this->config->filesystem}")) {
                            $filesystem       = "Idno\\Files\\{$this->config->filesystem}";
                            $this->filesystem = new $filesystem();
                        }
                        if (empty($this->filesystem)) {
                            if ($fs = $this->db()->getFilesystem()) {
                                $this->filesystem = $fs;
                            }
                        }
                        break;
                }

                $this->logging = new Logging();
                $this->config->load();

                if (isset($this->config->loglevel) && $this->logging instanceof Logging) {
                    $this->logging->setLogLevel($this->config->loglevel);
                }

                $this->session      = new Session();
                $this->actions      = new Actions();
                $this->template     = new Template();
                $this->language     = new Language();
                    $this->language()->register(new GetTextTranslation()); // Register default gettext translations
                $this->syndication  = new Syndication();
                $this->reader       = new Reader();
                $this->helper_robot = new HelperRobot();
                $this->queue        = $this->componentFactory($this->config->event_queue, "Idno\\Core\\EventQueue", "Idno\\Core\\", "Idno\\Core\\SynchronousQueue");
                $this->statistics   = $this->componentFactory($this->config->statistics_collector, "Idno\\Stats\\StatisticsCollector", "Idno\\Stats\\", "Idno\\Stats\\DummyStatisticsCollector");
                
                // Log some page statistics
                \Idno\Stats\Timer::start('script');
                register_shutdown_function(function () {
                    $stats = \Idno\Core\Idno::site()->statistics();
                    if (!empty($stats)) {
                        $stats->timing('timer.script', \Idno\Stats\Timer::value('script'));
                    }
                });
                
                // Attempt to create a cache object, making use of support present on the system
                if (extension_loaded('apc') && ini_get('apc.enabled'))
                    $this->cache = new \Idno\Caching\APCuCache();
                elseif (extension_loaded('xcache')) {
                    $this->cache = new \Idno\Caching\XCache();
                }
                else {
                    // Ensure there is always a cache available
                    $this->cache = new \Idno\Caching\FilesystemCache();
                } 
                // TODO: Support other persistent caching methods

                // No URL is a critical error, default base fallback is now a warning (Refs #526)
                if (!defined('KNOWN_CONSOLE')) {
                    if (!$this->config->url) throw new \Idno\Exceptions\ConfigurationException('Known was unable to work out your base URL! You might try setting url="http://yourdomain.com/" in your config.ini');
                    if ($this->config->url == '/') $this->logging->warning('Base URL has defaulted to "/" because Known was unable to detect your server name. '
                        . 'This may be because you\'re loading Known via a script. '
                        . 'Try setting url="http://yourdomain.com/" in your config.ini to remove this message');
                }

                // Connect to a Known hub if one is listed in the configuration file
                // (and this isn't the hub!)
                if (empty(site()->session()->hub_connect)) {
                    site()->session()->hub_connect = 0;
                }
                if (
                    !empty($this->config->known_hub) &&
                    !substr_count($_SERVER['REQUEST_URI'], '.') &&
                    $this->config->known_hub != $this->config->url
                ) {
                    site()->session()->hub_connect     = time();
                    \Idno\Core\Idno::site()->known_hub = new \Idno\Core\Hub($this->config->known_hub);
                    \Idno\Core\Idno::site()->known_hub->connect();
                }

                User::registerEvents();
            }

            /**
             * Registers some core page URLs
             */
            function registerPages()
            {
                $permalink_route = \Idno\Common\Entity::getPermalinkRoute();

                /** Homepage */
                $this->addPageHandler('/?', '\Idno\Pages\Homepage');
                $this->addPageHandler('/feed\.xml', '\Idno\Pages\Feed');
                $this->addPageHandler('/feed/?', '\Idno\Pages\Feed');
                $this->addPageHandler('/rss\.xml', '\Idno\Pages\Feed');
                $this->addPageHandler('/content/([A-Za-z\-\/]+)+', '\Idno\Pages\Homepage');

                /** Individual entities / posting / deletion */
                $this->addPageHandler('/view/([\%A-Za-z0-9]+)/?', '\Idno\Pages\Entity\View');
                $this->addPageHandler('/s/([\%A-Za-z0-9]+)/?', '\Idno\Pages\Entity\Shortlink');
                $this->addPageHandler($permalink_route . '/?', '\Idno\Pages\Entity\View');
                $this->addPageHandler('/edit/([A-Za-z0-9]+)/?', '\Idno\Pages\Entity\Edit');
                $this->addPageHandler('/delete/([A-Za-z0-9]+)/?', '\Idno\Pages\Entity\Delete');
                $this->addPageHandler('/withdraw/([A-Za-z0-9]+)/?', '\Idno\Pages\Entity\Withdraw');
                
                $this->addPageHandler('/attachment/([A-Za-z0-9]+)/([A-Za-z0-9]+)/?', '\Idno\Pages\Entity\Attachment\Delete');

                /** Annotations */
                $this->addPageHandler('/view/([A-Za-z0-9]+)/annotations/([A-Za-z0-9]+)?', '\Idno\Pages\Annotation\View');
                $this->addPageHandler($permalink_route . '/annotations/([A-Za-z0-9]+)?', '\Idno\Pages\Annotation\View');
                $this->addPageHandler($permalink_route . '/annotations/([A-Za-z0-9]+)/delete/?', '\Idno\Pages\Annotation\Delete'); // Delete annotation
                $this->addPageHandler('/annotation/post/?', '\Idno\Pages\Annotation\Post');

                /** Bookmarklets and sharing */
                $this->addPageHandler('/share/?', '\Idno\Pages\Entity\Share');
                $this->addPageHandler('/bookmarklet\.js', '\Idno\Pages\Entity\Bookmarklet', true);

                /** Mobile integrations */
                $this->addPageHandler('/chrome/manifest\.json', '\Idno\Pages\Chrome\Manifest', true);
                
                /** Service worker */
                $this->addPageHandler('/service-worker(\.min)?\.js', '\Idno\Pages\Chrome\ServiceWorker', true);

                /** Files */
                $this->addPageHandler('/file/upload/?', '\Idno\Pages\File\Upload', true);
                $this->addPageHandler('/file/picker/?', '\Idno\Pages\File\Picker', true);
                $this->addPageHandler('/filepicker/?', '\Idno\Pages\File\Picker', true);
                $this->addPageHandler('/file/([A-Za-z0-9]+)(/.*)?', '\Idno\Pages\File\View', true);

                /** Users */
                $this->addPageHandler('/profile/([^\/]+)/?', '\Idno\Pages\User\View');
                $this->addPageHandler('/profile/([^\/]+)/edit/?', '\Idno\Pages\User\Edit');

                /** Search */
                $this->addPageHandler('/search/?', '\Idno\Pages\Search\Forward');
                $this->addPageHandler('/search/mentions\.json', '\Idno\Pages\Search\Mentions');
                $this->addPageHandler('/tag/([^\s]+)\/?', '\Idno\Pages\Search\Tags');
		$this->addPageHandler('/search/users/?', '\Idno\Pages\Search\User');

                /** robots.txt */
                $this->addPageHandler('/robots\.txt', '\Idno\Pages\Txt\Robots');

                /** Autosave / preview */
                $this->addPageHandler('/autosave/?', '\Idno\Pages\Entity\Autosave');

                /** Installation / first use */
                $this->addPageHandler('/begin/?', '\Idno\Pages\Onboarding\Begin', true);
                $this->addPageHandler('/begin/register/?', '\Idno\Pages\Onboarding\Register', true);
                $this->addPageHandler('/begin/profile/?', '\Idno\Pages\Onboarding\Profile');
                $this->addPageHandler('/begin/connect/?', '\Idno\Pages\Onboarding\Connect');
                $this->addPageHandler('/begin/connect\-forwarder/?', '\Idno\Pages\Onboarding\ConnectForwarder');
                $this->addPageHandler('/begin/publish/?', '\Idno\Pages\Onboarding\Publish');

                /** Add some services */
                $this->addPageHandler('/service/db/optimise/?', '\Idno\Pages\Service\Db\Optimise');
                $this->addPageHandler('/service/vendor/messages/?', '\Idno\Pages\Service\Vendor\Messages');
                $this->addPageHandler('/service/security/csrftoken/?', '\Idno\Pages\Service\Security\CSRFToken');
                $this->addPageHandler('/service/web/unfurl/?', '\Idno\Pages\Service\Web\UrlUnfurl');
                $this->addPageHandler('/service/web/unfurl/remove/([a-zA-Z0-9]+)/?', '\Idno\Pages\Service\Web\RemovePreview');
                $this->addPageHandler('/service/web/imageproxy/([^\/]+)/?', '\Idno\Pages\Service\Web\ImageProxy');
                $this->addPageHandler('/service/web/imageproxy/([^\/]+)/([0-9]+)/?', '\Idno\Pages\Service\Web\ImageProxy'); // With scale
                $this->addPageHandler('/service/web/imageproxy/([^\/]+)/([0-9]+)/([^\/]+)/?', '\Idno\Pages\Service\Web\ImageProxy'); // With scale, with transform
                $this->addPageHandler('/service/system/log/?', '\Idno\Pages\Service\System\Log');

                // These must be loaded last
                $this->plugins = new Plugins();
                $this->themes  = new Themes();

            }

            /**
             * Return the database layer loaded as part of this site
             * @return \Idno\Core\DataConcierge
             */

            function &db()
            {
                return $this->db;
            }

            /**
             * Return the event dispatcher loaded as part of this site
             * @return \Symfony\Component\EventDispatcher\EventDispatcher
             */

            function &events()
            {
                return $this->dispatcher;
            }

            /**
             * Access to the EventQueue for dispatching events
             * asynchronously
             * @return \Idno\Core\EventQueue
             */
            function &queue()
            {
                return $this->queue;
            }

            /**
             * Returns the current filesystem
             * @return \Idno\Files\FileSystem
             */
            function &filesystem()
            {
                return $this->filesystem;
            }

            /**
             * Returns the current Known hub
             * @return \Idno\Core\Hub
             */
            function &hub()
            {
                return $this->known_hub;
            }

            /**
             * Returns the current logging interface
             * @return \Psr\Log\LoggerInterface
             */
            function &logging()
            {
                return $this->logging;
            }

            /**
             * Return a persistent cache object.
             * @return \Idno\Caching\PersistentCache
             */
            function &cache()
            {
                return $this->cache;
            }
            
            /**
             * Return a statistics collector
             * @return \Idno\Stats\StatisticsCollector
             */
            function &statistics()
            {
                return $this->statistics;
            }

            /**
             * Shortcut to trigger an event: supply the event name and
             * (optionally) an array of data, and get a variable back.
             *
             * @param string $eventName The name of the event to trigger
             * @param array $data Data to pass to the event
             * @param mixed $default Default response (if not forwarding)
             * @return mixed
             */

            function triggerEvent($eventName, $data = array(), $default = true)
            {
                $stats = $this->statistics();
                if (!empty($stats)) {
                    $stats->increment ("event.$eventName");
                }
                
                $event = new Event($data);
                $event->setResponse($default);
                $event = $this->events()->dispatch($eventName, $event);
                if (!$event->forward()) {
                    return $event->response();
                } else {
                    header('Location: ' . $event->forward());
                    exit;
                }
            }

            /**
             * Helper function that returns the current configuration object
             * for this site (or a configuration setting value)
             *
             * @param The configuration setting value to retrieve (optional)
             *
             * @return \Idno\Core\Config
             */
            function &config($setting = false)
            {
                if ($setting === false)
                    return $this->config;
                else
                    return $this->config->$setting;
            }

            /**
             * Helper function that returns the current syndication object for this site
             * @return \Idno\Core\Syndication
             */
            function &syndication()
            {
                return $this->syndication;
            }

            /**
             * Return the session handler associated with this site
             * @return \Idno\Core\Session
             */

            function &session()
            {
                return $this->session;
            }

            /**
             * Return the plugin handler associated with this site
             * @return \Idno\Core\Plugins
             */
            function &plugins()
            {
                return $this->plugins;
            }

            /**
             * Return the theme handler associated with this site
             * @return \Idno\Core\Themes
             */
            function &themes()
            {
                return $this->themes;
            }

            /**
             * Return the template handler associated with this site
             * @return \Idno\Core\Template
             */

            function &template()
            {
                return $this->template;
            }

            /**
             * Return the language handler associated with this site
             * @return \Idno\Core\Language
             */
            function &language()
            {
                if (empty($this->language)) {
                    $this->language = new Language();
                }

                return $this->language;
            }

            /**
             * Return the action helper associated with this site
             * @return \Idno\Core\Actions
             */
            function &actions()
            {
                return $this->actions;
            }

            /**
             * Return the reader associated with this site
             * @return \Idno\Core\Reader
             */
            function &reader()
            {
                return $this->reader;
            }

            /**
             * Tells the system that callable $listener wants to be notified when
             * event $event is triggered. $priority is an optional integer
             * that specifies order priority; the higher the number, the earlier
             * in the chain $listener will be notified.
             *
             * @param string $event
             * @param callable $listener
             * @param int $priority
             */

            function addEventHook($event, $listener, $priority = 0)
            {
                if (is_callable($listener)) {
                    $this->dispatcher->addListener($event, $listener, $priority);
                }
            }

            /**
             * Registers a page handler for a given pattern, using Toro
             * page handling syntax
             *
             * @param string $pattern The pattern to match
             * @param string $handler The name of the Page class that will serve this route
             * @param bool $public If set to true, this page is always public, even on non-public sites
             */

            function addPageHandler($pattern, $handler, $public = false)
            {
                if (defined('KNOWN_SUBDIRECTORY')) {
                    if (substr($pattern, 0, 1) != '/') {
                        $pattern = '/' . $pattern;
                    }
                    $pattern = '/' . KNOWN_SUBDIRECTORY . $pattern;
                }
                if (class_exists($handler)) {
                    $this->pagehandlers[$pattern] = $handler;
                    if ($public == true) {
                        $this->public_pages[] = $handler;
                    }
                } else {
                    $this->logging()->error("Could not add $pattern. $handler not found");
                }
            }

            /**
             * Registers a page handler for a given pattern, using Toro
             * page handling syntax - and ensures it will be handled first
             *
             * @param string $pattern The pattern to match
             * @param string $handler The name of the Page class that will serve this route
             * @param bool $public If set to true, this page is always public, even on non-public sites
             */
            function hijackPageHandler($pattern, $handler, $public = false)
            {
                if (class_exists($handler)) {
                    unset($this->pagehandlers[$pattern]);
                    unset($this->public_pages[$pattern]);
                    $this->pagehandlers = array($pattern => $handler) + $this->pagehandlers;
                    if ($public == true) {
                        $this->public_pages = array($pattern => $handler) + $this->public_pages;
                    }
                }
            }

            /**
             * Mark a page handler class as offering public content even on walled garden sites
             * @param $class
             */
            function addPublicPageHandler($class)
            {
                if (class_exists($class)) {
                    $this->public_pages[] = $class;
                }
            }

            /**
             * Retrieve an array of walled garden page handlers
             * @return array
             */
            function getPublicPageHandlers()
            {
                if (!empty($this->public_pages)) {
                    return $this->public_pages;
                }

                return array();
            }

            /**
             * Does the specified page handler class represent a public page, even on walled gardens?
             * @param $class
             * @return bool
             */
            function isPageHandlerPublic($class)
            {
                if (!empty($class)) {
                    if (in_array($class, $this->getPublicPageHandlers())) {
                        return true;
                    }
                    if ($class[0] != "\\") {
                        $class = "\\" . $class;
                        if (in_array($class, $this->getPublicPageHandlers())) {
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
             * @param string $path_info The path, including the initial /, or the URL
             * @return bool|\Idno\Common\Page
             */

            function getPageHandler($path_info)
            {
                $path_info = parse_url($path_info, PHP_URL_PATH);
                if ($q = strpos($path_info, '?')) {
                    $path_info = substr($path_info, 0, $q);
                }
                $tokens             = array(
                    ':string' => '([a-zA-Z]+)',
                    ':number' => '([0-9]+)',
                    ':alpha'  => '([a-zA-Z0-9-_]+)'
                );
                $discovered_handler = false;
                $matches            = array();
                foreach ($this->pagehandlers as $pattern => $handler_name) {
                    $pattern = strtr($pattern, $tokens);
                    if (preg_match('#^/?' . $pattern . '/?$#', $path_info, $matches)) {
                        $discovered_handler = $handler_name;
                        $regex_matches      = $matches;
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
             * Sets the current page (if any) for access throughout the system
             * @param \Idno\Common\Page $page
             */
            function setCurrentPage($page)
            {
                $this->currentPage = $page;
            }

            /**
             * Retrieve the current page
             * @return bool|\Idno\Common\Page
             */
            function currentPage()
            {
                if (!empty($this->currentPage)) {
                    return $this->currentPage;
                }

                return false;
            }

            /**
             * Can a specified user (either an explicitly specified user ID
             * or the currently logged-in user if this is left blank) edit
             * this entity?
             *
             * In this instance this specifically means "Can a given user create
             * new content or
             *
             * @param string $user_id
             * @return true|false
             */

            function canEdit($user_id = '')
            {

                if (!\Idno\Core\Idno::site()->session()->isLoggedOn()) return false;

                if (empty($user_id)) {
                    $user_id = \Idno\Core\Idno::site()->session()->currentUserUUID();
                }

                if ($user = \Idno\Entities\User::getByUUID($user_id)) {

                    if ($user->isAdmin()) {
                        return true;
                    }

                }

                return false;
            }

            /**
             * Can a specified user (either an explicitly specified user ID
             * or the currently logged-in user if this is left blank) publish
             * to the site?
             *
             * @param string $user_id
             * @return true|false
             */

            function canWrite($user_id = '')
            {
                if (!\Idno\Core\Idno::site()->session()->isLoggedOn()) return false;

                if (empty($user_id)) {
                    $user_id = \Idno\Core\Idno::site()->session()->currentUserUUID();
                }

                if ($user = \Idno\Entities\User::getByUUID($user_id)) {

                    // Remote users can't ever create anything :( - for now
                    if ($user instanceof \Idno\Entities\RemoteUser) {
                        return false;
                    }

                    // But local users can
                    if ($user instanceof \Idno\Entities\User) {
                        if (empty($user->read_only)) {
                            return true;
                        }
                    }

                }

                return false;
            }

            /**
             * Can a specified user (either an explicitly specified user ID
             * or the currently logged-in user if this is left blank) view
             * this entity?
             *
             * Always returns true at the moment, but might be a good way to build
             * walled garden functionality.
             *
             * @param string $user_id
             * @return true|false
             */

            function canRead($user_id = '')
            {
                return true;
            }

            /**
             * Retrieve site icons.
             * Retrieve a set of one or more icon for the current site, allowing plugins and other components
             * access icons for displaying in various contexts
             *
             * @returns array An associative array of various icons => url
             */
            function getSiteIcons()
            {
                $icons = [];

                // Set our defaults (TODO: Set these cleaner, perhaps through the template system)
                $icons['defaults'] = [
                    'default'     => \Idno\Core\Idno::site()->config()->getDisplayURL() . 'gfx/logos/logo_k.png',
                    'default_16'  => \Idno\Core\Idno::site()->config()->getDisplayURL() . 'gfx/logos/logo_k_16.png',
                    'default_32'  => \Idno\Core\Idno::site()->config()->getDisplayURL() . 'gfx/logos/logo_k_32.png',
                    'default_36'  => \Idno\Core\Idno::site()->config()->getDisplayURL() . 'gfx/logos/logo_k_36.png',
                    'default_48'  => \Idno\Core\Idno::site()->config()->getDisplayURL() . 'gfx/logos/logo_k_48.png',
                    'default_64'  => \Idno\Core\Idno::site()->config()->getDisplayURL() . 'gfx/logos/logo_k_64.png',
                    'default_96'  => \Idno\Core\Idno::site()->config()->getDisplayURL() . 'gfx/logos/logo_k_96.png',

                    // Apple logos
                    'default_57'  => \Idno\Core\Idno::site()->config()->getDisplayURL() . 'gfx/logos/apple-icon-57x57.png',
                    'default_72'  => \Idno\Core\Idno::site()->config()->getDisplayURL() . 'gfx/logos/apple-icon-72x72.png',
                    'default_114' => \Idno\Core\Idno::site()->config()->getDisplayURL() . 'gfx/logos/apple-icon-114x114.png',
                    'default_144' => \Idno\Core\Idno::site()->config()->getDisplayURL() . 'gfx/logos/apple-icon-144x144.png',
                    
                    'default_192' => \Idno\Core\Idno::site()->config()->getDisplayURL() . 'gfx/logos/logo_k_192.png',
                ];

                // If we're on a page, see if that has a specific icon
                if ($page = \Idno\Core\Idno::site()->currentPage()) {
                    if ($page_icons = $page->getIcon()) {
                        $icons['page'] = $page_icons;
                    }
                }

                // Now, return a list of icons, but pass it through an event hook to override
                return $this->triggerEvent('site/icons', ['object' => $this], $icons);
            }

            /**
             * Retrieve notices (eg notifications that a new version has been released) from Known HQ
             * @return mixed
             * @deprecated Use Vendor::getMessages()
             */
            function getVendorMessages()
            {
                \Idno\Core\Idno::site()->logging()->warning("DEPRECATION WARNING: Use Vendor::getMessages()");

                return Vendor::getMessages();

            }

            /**
             * Is this site being run in embedded mode? Hides the navigation bar, maybe more.
             * @return bool
             */
            function embedded()
            {
                if (site()->currentPage()->getInput('unembed')) {
                    $_SESSION['embedded'] = false;

                    return false;
                }
                if (!empty($_SESSION['embedded'])) {
                    return true;
                }
                if (site()->currentPage()->getInput('embedded')) {
                    $_SESSION['embedded'] = true;

                    return true;
                }

                return false;
            }

            /**
             * Detects if this site is being accessed securely or not
             * @return bool
             * @deprecated Duplicate of Page::isSSL()
             */
            function isSecure()
            {
                \Idno\Core\Idno::site()->logging()->warning("DEPRECATION WARNING: This is a duplicate of Page::isSSL() and will be removed shortly.");
                return Page::isSSL();
                
//                return
//                    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
//                    || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
//                    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https');
            }
            
            /**
             * Apply updates.
             */
            function upgrade() {
                
                $last_update = 0;
                if (!empty($this->config()->update_version)) {
                    $last_update = $this->config()->update_version;
                }
                $machine_version = Version::build();
                
                if ($last_update < $machine_version) {
                
                    if ($this->triggerEvent('upgrade', [
                        'last_update' => $last_update,
                        'new_version' => $machine_version
                    ])) {
                    
                        // Save updated
                        $this->config()->update_version = $machine_version;
                        $this->config()->save();
                        
                        $this->logging()->info("Known upgraded from $last_update to $machine_version");
                    } else {
                        $this->logging()->error("There was a problem applying an update.");
                    }
                }
            }

            /**
             * This is a state dependant object, and so can not be serialised.
             * @return array
             */
            function __sleep()
            {
                return [];
            }

            /**
             * Helper method that returns the current site object
             * @return \Idno\Core\Idno $site
             */
            static function &site()
            {
                return self::$site;
            }

            /**
             * Attempt to construct a component.
             * This allows for config configurable, and plugin extensible, system conponents, without the need for a lot of repeat typing.
             * @param string $className Class name of component, either partial or full namespace
             * @param string $expectedBaseClass Class type to verify newly created component against
             * @param string $defaultClassNameBase If a full namespace is not provided in $configValue, use this value as base class namespace
             * @param string $defaultClass If class could not be constructed, return a new instance of this class name
             */
            public function componentFactory($className, $expectedBaseClass = "Idno\\Common\\Component" , $defaultClassNameBase = "Idno\\Core\\", $defaultClass = null) {
                
                $component = null;
                
                // Try full namespace
                if (class_exists($className)) {
                    if (is_subclass_of($className, $expectedBaseClass)) {
                        $class = $className;
                    }
                }
                
                // Attempt base class creation
                if (empty($class)) {
                    if (class_exists($defaultClassNameBase . $className)) {
                        $class = $defaultClassNameBase . $className; 
                    } 
                } 
                
                // Now try and create it
                if (!empty($class)) {
                    if (is_subclass_of($class, $expectedBaseClass)) {
                        $component = new $class();
                    } 
                }
                
                // Do we have a class yet? otherwise try a default
                if (empty($component)) {
                
                    if (!empty($defaultClass)) {

                        if (is_string($defaultClass)) 
                            $component = new $defaultClass();
                        else
                            $component = $defaultClass;
                        
                        // validate
                        if (!is_subclass_of($component, $expectedBaseClass))
                                $component = null;
                    }
                }
                
                return $component;
            }
        }

        /**
         * Helper function that returns the current site object
         * @deprecated Use \Idno\Core\Idno::site()
         * @return \Idno\Core\Idno $site
         */
        function &site()
        {
            return \Idno\Core\Idno::$site;
        }

    }
