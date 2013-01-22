<?php

    /**
     * Session management class
     * 
     * @package idno
     * @subpackage core
     */

	namespace Idno\Core {
	
	    class Sessions extends \Idno\Common\Component {
		
		function init() {
		    session_start();
		}
		
		/**
		 * Is a user logged into the current session?
		 * @return true|false
		 */
		    function isLoggedIn() {
			if (!empty($_SESSION['user']) && $_SESSION['user'] instanceof \Idno\Entities\User) {
			    return true;
			}
			return false;
		    }
		
	    }
	    
	}