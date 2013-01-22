<?php

    /**
     * Configuration management class
     * 
     * @package idno
     * @subpackage core
     */

	namespace Idno\Core {
	    
	    class Config extends \Idno\Common\Component {
		
		public $config = array(
		    'dbname'    => 'idno',	    // Default MongoDB database
		);
		
		function init() {
		    // Load the config.ini file in the root folder, if it exists.
		    // If not, we'll use default values. No skin off our nose.
		    $this->path = dirname(dirname(dirname(__FILE__)));
		    if ($config = @parse_ini_file($this->path . '/config.ini')) {
			$this->config = array_merge($this->config, $config);
		    }
		}
		
		/**
		 * We're overloading the "get" method for the configuration
		 * class, so you can simply check $config->property to get
		 * a configuration value.
		 */
		
		function __get($name) {
		    return $this->config[$name];
		}
		
		/**
		 * Overloading the "set" method for the configuration class,
		 * so you can simply set $configuration->property = $value to
		 * overwrite configuration values.
		 */
		
		function __set($name, $value) {
		    return $this->config[$name] = $value;
		}
		
	    }
	    
	}