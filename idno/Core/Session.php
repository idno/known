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
		    
		    // Register log in, log out
		    site()->addPageHandler('/session/login', '\Idno\Pages\Session\Login');
		    site()->addPageHandler('/session/logout', '\Idno\Pages\Session\Logout');
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
		 * Wrapper function for isLoggedIn()
		 * @see Idno\Core\Session::isLoggedIn()
		 * @return true|false
		 */
		    
		    function isLoggedOn() { return $this->isLoggedIn(); }
		    
		/**
		 * Returns the currently logged-in user, if any
		 * @return Idno\Entities\User
		 */
		    
		    function currentUser() { 
			if (!empty($_SESSION['user']))
			    return $_SESSION['user']; 
			return false;
		    }
		    
		/**
		 * Get the UUID of the currently logged-in user, or false if
		 * we're logged out
		 * 
		 * @return mixed
		 */
		    
		    function currentUserUUID() {
			if ($this->isLoggedOn()) {
			    return $this->currentUser()->getUUID();
			}
			return false;
		    }
		    
		/**
		 * Get access groups the current user is allowed to write to
		 * @return array
		 */
		    
		    function getWriteAccessGroups() {
			if ($this->isLoggedOn())
			    return $this->currentUser ()->getWriteAccessGroups ();
			return array();
		    }
		    
		/**
		 * Get IDs of the access groups the current user is allowed to write to
		 * @return array
		 */
		    
		    function getWriteAccessGroupIDs() {
			if ($this->isLoggedOn())
			    return $this->currentUser ()->getWriteAccessGroups ();
			return array();
		    }
		    
		/**
		 * Get access groups the current user (if any) is allowed to read from
		 * @return array
		 */
		    
		    function getReadAccessGroups() {
			if ($this->isLoggedOn())
			    return $this->currentUser ()->getReadAccessGroups ();
			return array('PUBLIC');
		    }
		    
		/**
		 * Get IDs of the access groups the current user (if any) is allowed to read from
		 * @return array
		 */
		    
		    function getReadAccessGroupIDs() {
			if ($this->isLoggedOn())
			    return $this->currentUser ()->getReadAccessGroupIDs ();
			return array('PUBLIC');
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
		    
		/**
		 * Checks HTTP request headers to see if the request has been properly
		 * signed for API access, and if so, log the user on
		 * @todo make this complete
		 * 
		 * @return true|false Whether the user could be logged in
		 */
		    
		    function APIlogin() {
			return false;
		    }
		
	    }
	    
	}