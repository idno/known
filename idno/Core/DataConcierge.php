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
		private $database = null;
		
		function init() {
		    $this->client = new \MongoClient();
		    $this->database = $this->client->selectDB(config()->dbname);
		}
		
		/**
		 * Saves an idno entity to the database, returning the _id
		 * field on success.
		 * 
		 * @param Entity $object 
		 */
		
		function saveObject($object) {
		    if ($object instanceof \Idno\Common\Entity) {
			if ($collection = $object->getCollection()) {
			    $collection_obj = $this->database->selectCollection($collection);
			    $array = $object->saveToArray();
			    if ($result = $collection_obj->save($array)) {
				if ($result['ok'] == 1) {
				    return $array['_id'];
				}
			    }
			}
		    }
		    return false;
		}
		
	    }
	    
	}