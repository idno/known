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
            'dbstring' => 'mongodb://localhost:27017',
            'dbname' => 'idno',         // Default MongoDB database
            'sessionname' => 'idno',    // Default session name
            'open_registration' => true,// Can anyone register for this system?
            'plugins' => array(         // Default plugins
                'Status'
            ),
            'items_per_page' => 10      // Default items per page
        );

        function init()
        {
            // Load the config.ini file in the root folder, if it exists.
            // If not, we'll use default values. No skin off our nose.
            // @TODO override settings from the database
            $this->path = dirname(dirname(dirname(__FILE__))); // Base path
            $this->url = (\Idno\Common\Page::isSSL() ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . '/'; // A naive default base URL
            $this->title = 'New idno site'; // A default name for the site
            $this->timezone = 'UTC';
            $this->host = parse_url($this->url, PHP_URL_HOST); // The site hostname, without parameters etc

            if ($config = @parse_ini_file($this->path . '/config.ini')) {
                $this->config = array_merge($this->config, $config);
            }
            date_default_timezone_set($this->timezone);
            setlocale(LC_ALL, 'en_US.UTF8');
        }

        /**
         * We're overloading the "get" method for the configuration
         * class, so you can simply check $config->property to get
         * a configuration value.
         */

        function __get($name)
        {
            if (isset($this->config[$name]))
                return $this->config[$name];
            return null;
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
         * Retrieves configuration information from the database, if possible.
         */
        function load() {
            if ($config = \Idno\Core\site()->db()->getAnyRecord('config')) {
                $config = (array) $config;
                if (is_array($config)) {
                    $this->config = array_merge($this->config, $config);
                }
            }
        }

        /**
         * Saves configuration information to the database, if possible.
         * @return true|false
         */
        function save() {
            $array = $this->config;
            unset($array['dbname']);    // Don't save database access info to the database
            if (\Idno\Core\site()->db()->saveRecord('config',$array)) {
                $this->load();
                return true;
            }
            return false;
        }

    }

}