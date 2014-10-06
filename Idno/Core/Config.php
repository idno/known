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
                'database'          => 'mongodb',
                'dbstring'          => 'mongodb://localhost:27017',
                'dbname'            => 'known', // Default MongoDB database
                'sessionname'       => 'known', // Default session name
                'open_registration' => true, // Can anyone register for this system?
                'plugins'           => array( // Default plugins
                                              'Status',
                                              'Text',
                                              'Photo',
                                              'Like',
                                              'Checkin',
                                              'Media'
                ),
                'themes'            => [],
                'antiplugins'       => [],
                'alwaysplugins'     => [],
                'items_per_page'    => 10, // Default items per page
                'experimental'      => false, // A common way to enable experimental functions still in development
                'multitenant'       => false,
                'default_config'    => true, // This is a trip-switch - changed to true if configuration is loaded from an ini file / the db
                'log_level'         => 4
            );

            public $ini_config = [];

            function init()
            {
                // Load the config.ini file in the root folder, if it exists.
                // If not, we'll use default values. No skin off our nose.
                $this->path               = dirname(dirname(dirname(__FILE__))); // Base path
                $this->url                = (\Idno\Common\Page::isSSL() ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . '/'; // A naive default base URL
                $this->title              = 'New Known site'; // A default name for the site
                $this->description        = 'A social website powered by Known'; // Default description
                $this->timezone           = 'UTC';
                $this->host               = parse_url($this->url, PHP_URL_HOST); // The site hostname, without parameters etc
                $this->feed               = $this->url . '?_t=rss';
                $this->indieweb_citation  = false;
                $this->indieweb_reference = false;
                $this->known_hub          = false;
                $this->session_path       = session_save_path(); // Session path when not storing sessions in the database

                $this->loadIniFiles();

                if ($this->multitenant) {
                    $dbname            = $this->dbname;
                    $this->host        = str_replace('www.', '', $this->host);
                    //$this->sessionname = preg_replace('/[^\da-z]/i', '', $this->host);
                    $this->dbname      = preg_replace('/[^0-9a-z\.\-\_]/i', '', $this->host);

                    // Known now defaults to not including periods in database names for multitenant installs. Add
                    // 'multitenant_periods = true' if you wish to override this.
                    if (empty($this->multitenant_periods)) {
                        $this->dbname      = str_replace('.', '_', $this->dbname);
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
                    $this->ini_config = [];
                    if ($config = @parse_ini_file($this->path . '/config.ini')) {
                        $this->ini_config = array_merge($config, $this->ini_config);
                    }
                    // Per domain configuration
                    if ($config = @parse_ini_file($this->path . '/' . $this->host . '.ini')) {
                        unset($this->ini_config['initial_plugins']);  // Don't let plugin settings be merged
                        unset($this->ini_config['alwaysplugins']);
                        unset($this->ini_config['antiplugins']);
                        $this->ini_config = array_merge($config, $this->ini_config);
                    }
                    if (file_exists($this->path . '/config.json')) {
                        if ($json = file_get_contents($this->path . '/config.json')) {
                            if ($json = json_decode($json, true)) {
                                $this->ini_config = array_merge($json, $this->ini_config);
                            }
                        }
                    }
                }

                if (!empty($this->ini_config)) {
                    $this->config         = array_merge($this->config, $this->ini_config);
                    $this->default_config = false;
                }

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
                unset($array['dbname']); // Don't save database access info to the database
                unset($array['path']); // Don't save the file path to the database
                unset($array['url']); // Don't save the URL to the database
                unset($array['host']); // Don't save the host to the database
                unset($array['feed']); // Don't save the feed URL to the database
                unset($array['uploadpath']); // Don't save the upload path to the database
                unset($array['session_path']); // Don't save the session path in the database

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
                        unset($config['path']);
                        unset($config['url']);
                        unset($config['host']);
                        unset($config['feed']);
                        unset($config['uploadpath']);
                        unset($config['initial_plugins']);
                        unset($config['antiplugins']);
                        unset($config['alwaysplugins']);
                        unset($config['session_path']); 
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

        }

    }