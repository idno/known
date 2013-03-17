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
		$t = \Idno\Core\site()->template();
		$t->body = $t->draw('session/login');
		$t->title = 'Sign in';
		$t->drawPage();
	    }
	    
	    function postContent() {
		// TODO: change this to actual basic login, of course
		$user = \Idno\Entities\User::getByHandle('benwerd');
		\Idno\Core\site()->session()->logUserOn($user);
	    }

	}
    
    }