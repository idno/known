<?php

    /**
     * Session management class
     * 
     * @package idno
     * @subpackage core
     */

	namespace Idno\Core {
	
	    class Session extends \Idno\Common\Component {
		
		function init() {
		    session_name(site()->config->sessionname);
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
		    
		/**
		 * Log the specified user on (note that this is NOT the same as taking the user's auth credentials)
		 * 
		 * @param Idno\Entities\User $user
		 * @return Idno\Entities\User 
		 */
		    
		    function logUserOn(\Idno\Entities\User $user) {
			$_SESSION['user'] = $user;
			return $user;
		    }
		    
		/**
		 * Log the current session user off
		 * @return true
		 */
		    
		    function logUserOff() {
			unset($_SESSION['user']);
			return true;
		    }
		
	    }
	    
	}