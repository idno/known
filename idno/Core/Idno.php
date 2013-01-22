<?php

    /**
     * Base Idno class
     * 
     * @package idno
     * @subpackage core
     */

	namespace Idno\Core {
	    
	    class Idno extends \Idno\Common\Component {
		
		public $db;
		public $config;
		public static $site;
		
		function init() {
		    $this->config = new Config();
		    $this->db = new DataConcierge();
		    self::$site = $this;
		}
		
		static function &site($site = null) {
		    if ($site instanceof Idno) {
			self::$site = $site;
		    }
		    return self::$site;
		}
		
	    }
	    
	    /**
	     * Helper function that returns the current configuration object
	     * for this site
	     * 
	     * @return Idno\Core\Config
	     */
		function &config() {
		    return \Idno\Core\Idno::$site->config;
		}
		
	    /**
	     * Helper function that returns the current site object
	     * @return Idno\Core\Idno
	     */
		function &site() {
		    return \Idno\Core\Idno::$site;
		}

	}