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
		private static $idno;
		
		function init() {
		    $this->db = new DataConcierge();
		    self::site($this);
		}
		
		static function &site($site = null) {
		    if ($site instanceof Idno) {
			self::$idno = $site;
		    }
		    return self::$idno;
		}
		
	    }

	}