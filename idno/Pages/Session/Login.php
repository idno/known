<?php

    /**
     * Defines built-in log in functionality
     */

    namespace Idno\Pages\Session {

	/**
	 * Default class to serve the homepage
	 */
	class Login extends \Idno\Core\Page {

	    function getContent() {
	    }
	    
	    function postContent() {
		$this->forward('/');
		//\Idno\Core\site()->session()->logUserOn($user);
	    }

	}
    
    }