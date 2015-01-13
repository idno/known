<?php

	/**
	 * 	Hello!
	 
	 	This is the simplest of all possible examples (ish). It'll draw a
	 	page of text in two template types.
	 	
	 	Stick ?t=rss on the end of the URL to load the same page in RSS.
	 	
	 */

	// Load Bonita
		require_once dirname(dirname(__FILE__)) . '/start.php';
	
	// Add this directory as an additional path
		\Bonita\Main::additionalPath(dirname(__FILE__));
		
	// Instantiate template
		$t = new \Bonita\Templates();
		
	// For the purposes of this example, some GET line fun to choose which template
	// we're using
		$t->detectTemplateType();
		if (!empty($_GET['t'])) {
		    $t->setTemplateType($_GET['t']);
		}
		
	// Page contents:
	
	// Page title
		$t->title = 'Example page';
		
	// A link back to git
		$t->url = 'https://github.com/benwerd/bonita';
		
	// Page body
		$t->body = $t->draw('pages/example');
		
	// And finally, draw the page
		$t->drawPage();
	
	// Or, we could have done it in one call, this way:	
	/*
		$t->__(array(
		
			'title' => 'Example page',
			'url' => 'https://github.com/benwerd/bonita',
			'body' => $t->draw('pages/example'),
			
		))->drawPage();
	*/