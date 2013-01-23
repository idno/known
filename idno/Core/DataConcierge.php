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
			    $array = $object->saveToArray();
			    return $this->saveRecord($collection, $array);
			}
		    }
		    return false;
		}
		
		/**
		 * Retrieves an Idno entity object by ID, casting it to the
		 * correct class
		 * 
		 * @param string $id
		 * @return Idno\Entity | false
		 */
		
		function getObject($id) {
		    if ($result = $this->getRecord($id)) {
			if (!empty($result['entity_subtype']))
			    if (class_exists($result['entity_subtype'])) {
				$object = new $result['entity_subtype']();
				$object->loadFromArray($result);
				return $object;
			    }
		    }
		    return false;
		}
		
		
		
		/**
		 * Retrieves a record from the database by ID
		 * 
		 * @param string $id
		 * @return array
		 */
		
		function getRecord($id) {
		    return $this->database->entities->findOne(array("_id" => new \MongoId($id)));
		}
		
		/**
		 * Saves a record to the specified database collection
		 * 
		 * @param string $collection
		 * @param array $array
		 * @return MongoID | false
		 */
		
		function saveRecord($collection, $array) {
		    $collection_obj = $this->database->selectCollection($collection);
		    if ($result = $collection_obj->save($array)) {
			if ($result['ok'] == 1) {
			    return $array['_id'];
			}
		    }
		    return false;
		}
		
	    }
	    
	    /**
	     * Helper function that returns the current database object
	     * @return Idno\Core\DataConcierge
	     */
		function db() {
		    return \Idno\Core\Idno::$site->db;
		}
	    
	}