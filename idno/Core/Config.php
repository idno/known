<?php

    /**
     * Configuration management class
     * 
     * @package idno
     * @subpackage core
     */

	namespace Idno\Core {
	
	    class Config extends \Idno\Common\Component {
		
		public $config;
		
		function init() {
		    
		    if ($config = parse_ini_file(dirname(dirname(dirname(__FILE__))) . '/config.ini')) {
			$this->config = array_merge($this->config, $config);
		    } 
		    
		}
		
	    }
	    
	}