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
            'dbname' => 'idno',      // Default MongoDB database
            'sessionname' => 'idno', // Default session name
            'plugins' => array(      // Default plugins
                'Text'
            )
        );

        function init()
        {
            // Load the config.ini file in the root folder, if it exists.
            // If not, we'll use default values. No skin off our nose.
            // @TODO override settings from the database
            $this->path = dirname(dirname(dirname(__FILE__))); // Base path
            $this->url = 'http://' . $_SERVER['SERVER_NAME'] . '/'; // A naive default base URL
            $this->title = 'New idno site'; // A default name for the site
            $this->timezone = 'UTC';
            if ($config = @parse_ini_file($this->path . '/config.ini')) {
                $this->config = array_merge($this->config, $config);
            }
            date_default_timezone_set($this->timezone);
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

    }

}