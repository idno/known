<?php

    /**
     * Defines built-in log out functionality
     */

    namespace Idno\Pages\Session {

	/**
	 * Default class to serve the homepage
	 */
	class Logout extends \Idno\Core\Page {

	    function getContent() {
	    }
	    
	    function postContent() {
		$result = \Idno\Core\site()->session()->logUserOff();
		$this->forward($_SERVER['HTTP_REFERER']);
		return $result;
	    }

	}
    
    }