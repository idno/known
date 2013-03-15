<?php

    /**
     * Defines the site homepage
     */

    namespace Idno\Pages {

	/**
	 * Default class to serve the homepage
	 */
	class Homepage extends \Idno\Core\Page {

	    // Handle GET requests to the homepage

	    function getContent() {
		$feed = \Idno\Entities\ActivityStreamPost::get();
		$t = \Idno\Core\site()->template();
		$t->__(array(

			    'title' => \Idno\Core\site()->config()->title,
			    'body' => $t->__(array(
						'feed' => $feed
					    ))->draw('pages/home'),

		    ))->drawPage();
	    }

	}
    
    }