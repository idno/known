<?php

	/**
	 * 	Howdy!
	 
		This is a really simple example of how to use forms.
		
		If you haven't already, check out index.php first.
	 	
	 */
	 
	// Load Bonita
		require_once dirname(dirname(__FILE__)) . '/start.php';
	
	// Add this directory as an additional path
		\Bonita\Main::additionalPath(dirname(__FILE__));
		
	// Instantiate template
		$t = new \Bonita\Templates();
		
	// Set the body
		$t->body = $t->draw('pages/forms');
		
	// Was the form already submitted?
		if (\Bonita\Forms::formSubmitted()) {
			
			// If so, validate the form token (to prevent nefarious tomfoolery)
			if (\Bonita\Forms::validateToken()) {
			
				// If the action completed, set the body to our form submission template
				$t->body = $t->draw('pages/example/formsubmitted');
			
			}
			
		}
		
	// Draw the page
		$t->__(array(
		
			'title' => 'Forms example'
			
		))->drawPage();