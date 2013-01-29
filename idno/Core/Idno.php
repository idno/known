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
		public $session;
		public static $site;
		
		function init() {
		    self::$site = $this;
		    $this->config = new Config();
		    $this->db = new DataConcierge();
		    $this->session = new Session();
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