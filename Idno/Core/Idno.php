<?php

    /**
     * Base Idno class
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Core {

        class Idno extends \Idno\Common\Component
        {

            public $db;
            public $filesystem;
            public $config;
            public $session;
            public $template;
            public $actions;
            public $plugins;
            public $dispatcher;
            public $pagehandlers;
            public $public_pages;
            public $syndication;
            public static $site;
            public $currentPage;

            function init()
            {
                self::$site       = $this;
                $this->dispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
                $this->config     = new Config();
                switch ($this->config->database) {
                    case 'mongodb':
                        $this->db = new DataConcierge();
                        break;
                    case 'mysql':
                        $this->db = new \Idno\Data\MySQL();
                        break;
                    default:
                        if (class_exists("Idno\\Data\\{$this->config->database}")) {
                            $db       = "Idno\\Data\\{$this->config->database}";
                            $this->db = new $db();
                        }
                        if (empty($this->db)) {
                            $this->db = new DataConcierge();
                        }
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
                $this->config->load();
                $this->session     = new Session();
                $this->actions     = new Actions();
                $this->template    = new Template();
                $this->syndication = new Syndication();
                $this->themes      = new Themes();
                $this->plugins     = new Plugins(); // This must be loaded last
            }

            /**
             * Registers some core page URLs
             */
            function registerpages()
            {

                $this->addPageHandler('/', '\Idno\Pages\Homepage');
                $this->addPageHandler('/content/([A-Za-z\-\/]+)+', '\Idno\Pages\Homepage');
                $this->addPageHandler('/view/([A-Za-z0-9]+)/?', '\Idno\Pages\Entity\View');
                $this->addPageHandler('/view/([A-Za-z0-9]+)/annotations/([A-Za-z0-9]+)?', '\Idno\Pages\Annotation\View');
                $this->addPageHandler('/s/([A-Za-z0-9]+)/?', '\Idno\Pages\Entity\Shortlink');
                $this->addPageHandler('/[0-9]+/([A-Za-z0-9\-\_]+)/?', '\Idno\Pages\Entity\View');
                $this->addPageHandler('/[0-9]+/([A-Za-z0-9\-\_]+)/annotations/([A-Za-z0-9]+)?', '\Idno\Pages\Annotation\View');
                $this->addPageHandler('/edit/([A-Za-z0-9]+)/?', '\Idno\Pages\Entity\Edit');
                $this->addPageHandler('/delete/([A-Za-z0-9]+)/?', '\Idno\Pages\Entity\Delete');
                $this->addPageHandler('/share/?', '\Idno\Pages\Entity\Share');
                $this->addPageHandler('/bookmarklet\.js', '\Idno\Pages\Entity\Bookmarklet', true);
                $this->addPageHandler('/[0-9]+/([A-Za-z0-9\-\_]+)/annotations/([A-Za-z0-9]+)/delete/?', '\Idno\Pages\Annotation\Delete'); // Delete annotation
                $this->addPageHandler('/annotation/post/?', '\Idno\Pages\Annotation\Post');
                $this->addPageHandler('/file/([A-Za-z0-9]+)(/.*)?', '\Idno\Pages\File\View', true);
                $this->addPageHandler('/profile/([^\/]+)/?', '\Idno\Pages\User\View');
                $this->addPageHandler('/profile/([^\/]+)/edit/?', '\Idno\Pages\User\Edit');
                $this->addPageHandler('/robots\.txt', '\Idno\Pages\Txt\Robots');
                $this->addPageHandler('/humans\.txt', '\Idno\Pages\Txt\Humans');
                $this->addPageHandler('/autosave/?', '\Idno\Pages\Entity\Autosave');
                $this->addPageHandler('/search/?', '\Idno\Pages\Search\Forward');
                $this->addPageHandler('/search/mentions\.json', '\Idno\Pages\Search\Mentions');

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
             * Returns the current filesystem
             * @return \Idno\Files\FileSystem
             */
            function &filesystem()
            {
                return $this->filesystem;
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
                $event = new Event($data);
		$event->setResponse($default);
                $this->events()->dispatch($eventName, $event);
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
             * Return the action helper associated with this site
             * @return \Idno\Core\Actions
             */
            function &actions()
            {
                return $this->actions;
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
                static $listened = [];
                if (is_callable($listener)) {
                    $listen_index = json_encode($listener);
                    if (empty($listened[$event][$listen_index])) {
                        $this->dispatcher->addListener($event, $listener, $priority);
                        $listened[$event][$listen_index] = true;
                    }
                }
            }

            /**
             * Registers a page handler for a given pattern, using Toro
             * page handling syntax
             *
             * @param string $pattern The pattern to match
             * @param callable $handler The handler callable that will serve the page
             * @param bool $public If set to true, this page is always public, even on non-public sites
             */

            function addPageHandler($pattern, $handler, $public = false)
            {
                if (class_exists($handler)) {
                    $this->pagehandlers[$pattern] = $handler;
                    if ($public == true) {
                        $this->public_pages[] = $handler;
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

                return [];
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
                if (substr_count($path_info, \Idno\Core\site()->config()->url)) {
                    $path_info = '/' . str_replace(\Idno\Core\site()->config()->url, '', $path_info);
                }
                if ($q = strpos($path_info, '?')) {
                    $path_info = substr($path_info, 0, $q);
                }
                $tokens             = array(
                    ':string' => '([a-zA-Z]+)',
                    ':number' => '([0-9]+)',
                    ':alpha'  => '([a-zA-Z0-9-_]+)'
                );
                $discovered_handler = false;
                $matches            = [];
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
                if (!empty($this->currentPage)) return $this->currentPage;

                return false;
            }

            /**
             * Retrieve this version of idno's version number
             * @return string
             */
            function version()
            {
                return '0.1-dev';
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

                if (!\Idno\Core\site()->session()->isLoggedOn()) return false;

                if (empty($user_id)) {
                    $user_id = \Idno\Core\site()->session()->currentUserUUID();
                }

                if ($user = \Idno\Entities\User::getByUUID($user_id)) {

                    // Remote users can't ever create anything :(
                    if ($user instanceof \Idno\Entities\RemoteUser)
                        return false;

                    // But local users can
                    if ($user instanceof \Idno\Entities\User)
                        return true;

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
        }

        /**
         * Helper function that returns the current site object
         * @return \Idno\Core\Idno
         */
        function &site()
        {
            return \Idno\Core\Idno::$site;
        }

    }