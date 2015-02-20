<?php

    /**
     * Configuration management class
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Core {

        class Config extends \Idno\Common\Component
        {

            public $config = array(
                'database'            => 'mongodb',
                'dbstring'            => 'mongodb://localhost:27017',
                'dbname'              => 'known', // Default MongoDB database
                'sessionname'         => 'known', // Default session name
                'open_registration'   => true, // Can anyone register for this system?
                'plugins'             => array( // Default plugins
                                                'Status',
                                                'Text',
                                                'Photo',
                                                'Like',
                                                'Checkin',
                                                'Media',
                                                'Firefox',
                                                'Bridgy'
                ),
                'themes'              => array(),
                'antiplugins'         => array(),
                'alwaysplugins'       => array(),
                'prerequisiteplugins' => array(),
                'items_per_page'      => 10, // Default items per page
                'experimental'        => false, // A common way to enable experimental functions still in development
                'multitenant'         => false,
                'default_config'      => true, // This is a trip-switch - changed to true if configuration is loaded from an ini file / the db
                'log_level'           => 4,
                'multi_syndication'   => true
            );

            public $ini_config = array();

            function init()
            {
                // Load the config.ini file in the root folder, if it exists.
                // If not, we'll use default values. No skin off our nose.
                $this->path                      = dirname(dirname(dirname(__FILE__))); // Base path
                $this->url                       = $this->detectBaseURL();
                $this->title                     = 'New Known site'; // A default name for the site
                $this->description               = 'A social website powered by Known'; // Default description
                $this->timezone                  = 'UTC';
                $this->host                      = parse_url($this->url, PHP_URL_HOST); // The site hostname, without parameters etc
                $this->feed                      = $this->getDisplayURL() . 'content/all/?_t=rss';
                $this->indieweb_citation         = false;
                $this->indieweb_reference        = false;
                $this->known_hub                 = false;
                $this->hub                       = 'http://withknown.superfeedr.com/';
                $this->session_path              = session_save_path(); // Session path when not storing sessions in the database
                $this->disable_cleartext_warning = false; // Set to true to disable warning when access credentials are sent in the clear
                $this->cookie_jar                = rtrim(sys_get_temp_dir(), '/\\') . DIRECTORY_SEPARATOR; // Cookie jar for API requests, default location isn't terribly secure on shared hosts!
                $this->multi_syndication         = true; // Do we allow multiple accounts per syndication endpoint?

                $this->loadIniFiles();

                if (substr($this->host,0,4) == 'www.') {
                    $this->host = substr($this->host,4);
                }

                if ($this->multitenant) {
                    $dbname     = $this->dbname;
                    $this->dbname = preg_replace('/[^0-9a-z\.\-\_]/i', '', $this->host);

                    // Known now defaults to not including periods in database names for multitenant installs. Add
                    // 'multitenant_periods = true' if you wish to override this.
                    if (empty($this->multitenant_periods)) {
                        $this->dbname = str_replace('.', '_', $this->dbname);
                    }

                    if (empty($this->dbname)) {
                        $this->dbname = $dbname;
                    }
                }

                if (!empty($this->initial_plugins)) {
                    if (!empty($this->default_plugins)) {
                        $this->default_plugins = array_merge($this->default_plugins, $this->initial_plugins);
                    } else {
                        $this->default_plugins = $this->initial_plugins;
                    }
                }
                if (!empty($this->default_plugins)) {
                    $this->plugins = $this->default_plugins;
                }

                date_default_timezone_set($this->timezone);
                //setlocale(LC_ALL, 'en_US.UTF8');
            }

            /**
             * Load configuration from ini files
             */
            function loadIniFiles()
            {

                if (empty($this->ini_config)) {
                    $this->ini_config = array();
                    if ($config = @parse_ini_file($this->path . '/config.ini')) {
                        $this->ini_config = array_merge($config, $this->ini_config);
                    }
                    if (file_exists($this->path . '/config.json')) {
                        if ($json = file_get_contents($this->path . '/config.json')) {
                            if ($json = json_decode($json, true)) {
                                $this->ini_config = array_merge($this->ini_config, $json);
                            }
                        }
                    }

                    // Per domain configuration
                    if ($config = @parse_ini_file($this->path . '/' . $this->host . '.ini')) {
                        unset($this->ini_config['initial_plugins']);  // Don't let plugin settings be merged
                        unset($this->ini_config['alwaysplugins']);
                        unset($this->ini_config['antiplugins']);
                        $this->ini_config = array_merge($this->ini_config, $config);
                    }

                    // Check environment variables and set as appropriate
                    foreach ($_SERVER as $name => $val) {
                        if (substr($name, 0, 6) == 'KNOWN_') {
                            $name                    = strtolower(str_replace('KNOWN_', '', $name));
                            $val                     = $val;
                            $this->ini_config[$name] = $val;
                        }
                    }

                    // Perform some sanity checks on some user contributed settings
                    if (isset($this->ini_config['uploadpath'])) $this->ini_config['uploadpath'] = rtrim($this->ini_config['uploadpath'], ' /') . '/'; // End trailing slash insanity once and for all
                    unset($this->ini_config['path']); // Path should always be derived
                    unset($this->ini_config['host']); // Host should always come from URL
                }

                if (!empty($this->ini_config)) {
                    $this->config         = array_merge($this->config, $this->ini_config);
                    $this->default_config = false;
                }

            }

            /**
             * Attempt to detect your known configuration's server name.
             */
            protected function detectBaseURL()
            {
                if (!empty($_SERVER['SERVER_NAME'])) {

                    // Servername specified, so we can construct things in the normal way.
                    return (\Idno\Common\Page::isSSL() ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . '/'; // A naive default base URL
                }

                // No servername set, try something else
                // TODO: Detect servername using other methods (but don't use HTTP_HOST)


                // Default to root relative urls
                return '/';
            }

            /**
             * We're overloading the "get" method for the configuration
             * class, so you can simply check $config->property to get
             * a configuration value.
             */

            function &__get($name)
            {
                return $this->config[$name];
            }

            /**
             * Overloading the "set" method for the configuration class,
             * so you can simply set $configuration->property = $value to
             * overwrite configuration values.
             */

            function __set($name, $value)
            {
                return $this->config[$name] = $value;
            }

            /**
             * Overloading the entity property isset check, so that
             * isset($entity->property) and empty($entity->property)
             * work as expected.
             */

            function __isset($name)
            {
                if (!empty($this->config[$name])) return true;

                return false;
            }

            /**
             * Saves configuration information to the database, if possible.
             * @return true|false
             */
            function save()
            {
                $array = $this->config;
                unset($array['dbname']); // Don't save database a
                unset($array['dbpass']);
                unset($array['dbhost']);
                unset($array['dbstring']);
                unset($array['path']); // Don't save the file path to the database
                unset($array['url']); // Don't save the URL to the database
                unset($array['host']); // Don't save the host to the database
                unset($array['feed']); // Don't save the feed URL to the database
                unset($array['uploadpath']); // Don't save the upload path to the database
                unset($array['session_path']); // Don't save the session path in the database
                unset($array['cookie_jar']); // Don't save the cookie path in the database
                unset($array['known_hub']);

                // If we don't have a site secret, create it
                if (!isset($array['site_secret']))
                    $array['site_secret'] = hash('sha256', mt_rand() . microtime(true));

                if (\Idno\Core\site()->db()->saveRecord('config', $array)) {
                    $this->load();

                    return true;
                }

                return false;
            }

            /**
             * Retrieves configuration information from the database, if possible - while
             * ensuring that config.ini overwrites db fields.
             */
            function load()
            {
                if ($config = \Idno\Core\site()->db()->getAnyRecord('config')) {
                    $this->default_config = false;
                    if ($config instanceof \Idno\Common\Entity) {
                        $config = $config->getAttributes();
                        unset($config['dbname']); // Ensure we don't accidentally load protected data from db
                        unset($config['dbpass']);
                        unset($config['dbhost']);
                        unset($config['dbstring']);
                        unset($config['path']);
                        unset($config['url']);
                        unset($config['host']);
                        unset($config['feed']);
                        unset($config['uploadpath']);
                        unset($config['initial_plugins']);
                        unset($config['antiplugins']);
                        unset($config['alwaysplugins']);
                        unset($config['session_path']);
                        unset($config['cookie_jar']);
                    }
                    if (is_array($config)) {
                        $this->config = array_merge($this->config, $config);
                    }
                }
                $this->loadIniFiles();
            }

            /**
             * Retrieve the canonical URL of the site
             * @return string
             */
            function getURL()
            {
                if (!empty($this->url)) {
                    return $this->url;
                } else {
                    return '/';
                }
            }

            /**
             * Return a version of the URL suitable for displaying in templates etc
             * @return mixed
             */
            function getDisplayURL()
            {
                $url       = $this->getURL();
                $urischeme = parse_url($url, PHP_URL_SCHEME);
                if (site()->isSecure()) {
                    $newuri = 'https:';
                } else {
                    $newuri = 'http:';
                }

                return str_replace($urischeme . ':', $newuri, $url);
            }

            /**
             * Does this site have SSL?
             * @return bool
             */
            function hasSSL()
            {
                if (substr_count(site()->config()->getURL(), 'https://') || !empty($this->config->force_ssl)) {
                    return true;
                }

                return false;
            }

            /**
             * Retrieve the name of this site
             * @return string
             */
            function getTitle()
            {
                if (!empty($this->title)) {
                    return $this->title;
                }

                return '';
            }

            /**
             * Return a normalized version of the host, for use in file paths etc
             * @return string
             */
            function pathHost()
            {
                return str_replace('www.', '', $this->host);
            }

            /**
             * Returns the base folder name to use when storing files (usually the site host)
             * @return mixed|string
             */
            function getFileBaseDirName()
            {
                $host = $this->pathHost();
                if (!empty($this->file_path_host)) {
                    $host = $this->file_path_host;
                }
                $host = site()->triggerEvent('file/path/host', ['host' => $host], $host);
                return $host;
            }

            /**
             * Is this site's content available to non-members?
             * @return bool
             */
            function isPublicSite()
            {
                if (empty($this->walled_garden)) {
                    return true;
                }

                return false;
            }

            /**
             * Does this site allow users to have multiple syndication accounts?
             * @return bool
             */
            function multipleSyndicationAccounts()
            {
                if (isset($this->multi_syndication)) {
                    return $this->multi_syndication;
                }

                return false;
            }

            /**
             * Can new users be added to the site? Defaults to true; uses a hook to determine.
             * @return bool
             */
            function canAddUsers()
            {
                $event = new Event();
                $event->setResponse(true);
                $event = site()->events()->dispatch('users/add/check', $event);

                return $event->response();
            }

            /**
             * Can the site administrator make this site private? Defaults to true; uses a hook to determine.
             * @return bool
             */
            function canMakeSitePrivate()
            {
                $event = new Event();
                $event->setResponse(true);
                $event = site()->events()->dispatch('site/walledgarden/check', $event);

                return $event->response();
            }

            /**
             * Is this the default site configuration?
             * @return bool
             */
            function isDefaultConfig()
            {
                if ($this->default_config) {
                    return true;
                }

                return false;
            }

            /**
             * Get the content types that this site should display on its homepage.
             * @return array
             */
            function getHomepageContentTypes()
            {
                $friendly_types = array();
                if ($temp_types = $this->default_feed_content) {
                    if (is_array($temp_types)) {
                        foreach ($temp_types as $temp_type) {
                            if ($content_type_class = \Idno\Common\ContentType::categoryTitleToClass($temp_type)) {
                                $friendly_types[] = $content_type_class;
                            }
                        }
                    }
                }

                return $friendly_types;
            }

            /**
             * Attempt to get a temporary folder suitable for writing in.
             * @return string
             */
            function getTempDir()
            {
                static $temp;

                if (function_exists('sys_get_temp_dir')) {
                    $temp = sys_get_temp_dir();
                    if (is_dir($temp)) {
                        return $this->sanitizePath($temp);
                    }
                }

                $temp = ini_get('upload_tmp_dir');
                if (@is_dir($temp)) {
                    return $this->sanitizePath($temp);
                }

                if (!empty($this->uploadpath)) {
                    $temp = $this->uploadpath;
                    if (is_dir($temp)) {
                        return $temp;
                    }
                }

                $temp = '/tmp/';

                return $temp;
            }

            /**
             * Add a trailing slash to the ends of paths
             * @todo Further sanitization tasks
             * @param $path
             * @return string
             */
            function sanitizePath($path)
            {
                if (substr($path, -1) != DIRECTORY_SEPARATOR) {
                    $path .= DIRECTORY_SEPARATOR;
                }

                return $path;
            }

        }

    }