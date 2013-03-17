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
		if ($user = \Idno\Entities\User::getByHandle($this->getInput('handle'))) {} 
		else if ($user = \Idno\Entities\User::getByEmail($this->getInput('email'))) {}
		else {
		    $this->setResponse(401);
		    $this->forward('/session/login');
		}
		
		if ($user instanceof \Idno\Entities\User) {
		    if ($user->checkPassword($this->getInput('password'))) {
			\Idno\Core\site()->session()->logUserOn($user);
			$this->forward();
		    }
		}
	    }

	}
    
    }