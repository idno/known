<?php

    /**
     * Data management class.
     * By default, we're using MongoDB. Of course, you're free to extend this,
     * implement the functions, and set Idno\Core\Idno->$db to be its
     * replacement.
     * 
     * @package idno
     * @subpackage core
     */

	namespace Idno\Core {
	
	    class DataConcierge extends \Idno\Common\Component {
		
		private $client = null;
		
		function init() {
		    $this->client = new \MongoClient();
		    // We should probably select the database, establish 
		    // collections, etc, here
		}
		
	    }
	    
	}