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
		    $this->database = $this->client->selectDB(site()->config()->dbname);
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
		 * Retrieves an Idno entity object by its UUID, casting it to the
		 * correct class
		 * 
		 * @param string $id
		 * @return Idno\Entity | false
		 */
		
		function getObject($uuid) {
		    if ($result = $this->getRecordByUUID($uuid)) {
			return $this->rowToEntity($result);
		    }
		    return false;
		}
		
		/**
		 * Converts a database row into an Idno entity
		 * 
		 * @param array $row
		 * @return Idno\Common\Entity
		 */
		function rowToEntity($row) {
		    if (!empty($row['entity_subtype']))
			    if (class_exists($row['entity_subtype'])) {
				$object = new $row['entity_subtype']();
				$object->loadFromArray($row);
				return $object;
			    }
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
		 * Retrieves a record from the database by its UUID
		 * 
		 * @param string $id
		 * @return array
		 */
		
		function getRecordByUUID($uuid) {
		    return $this->database->entities->findOne(array("uuid" => $uuid));
		}
		
		/**
		 * Retrieves a set of records from the database with given parameters, in 
		 * reverse chronological order
		 * 
		 * @param array $parameters Query parameters in MongoDB format
		 * @param int $limit Maximum number of records to return
		 * @param int $offset Number of records to skip
		 * @return iterator|false Iterator or false, depending on success
		 */
		
		function getRecords($fields, $parameters, $limit, $offset) {
		    if ($result = $this->database->entities->find($parameters, $fields)->skip($offset)->limit($limit)->sort(array('created' => -1))) {
			return $result;
		    }
		    return false;
		}
		
		/**
		 * Retrieve objects of a certain kind that we're allowed to see,
		 * in reverse chronological order
		 * 
		 * @param string|array $subtypes String or array of subtypes we're allowed to see
		 * @param array $search Any extra search terms in array format (eg array('foo' => 'bar')) (default: empty)
		 * @param array $fields An array of fieldnames to return (leave empty for all; default: all)
		 * @param int $limit Maximum number of records to return (default: 10)
		 * @param int $offset Number of records to skip (default: 0)
		 * @return array|false Array of elements or false, depending on success
		 */
		
		function getObjects($subtypes = '', $search = array(), $fields = array(), $limit = 10, $offset = 0) {
		    
		    // Initialize query parameters to be an empty array
		    $query_parameters = array();
		    
		    // Ensure subtypes are recorded properly
		    if (!empty($subtypes)) {
			if (is_array($subtypes)) {
			    $query_parameters['entity_subtype'] = array('$in' => $subtypes);
			} else {
			    $query_parameters['entity_subtype'] = $subtypes;
			}
		    }
		    
		    // Make sure we're only getting objects that we're allowed to see
		    $readGroups = site()->session()->getReadAccessGroupIDs();
		    //$query_parameters['access'] = array('$in' => $readGroups);
		    
		    // Join the rest of the search query elements to this search
		    $query_parameters = array_merge($query_parameters, $search);
		    
		    // Prepare the fields array for searching, if required
		    if (!empty($fields) && is_array($fields)) {
			$fields = array_flip($fields);
			$fields = array_fill_keys($fields, true);
		    } else {
			$fields = array();
		    }
		    
		    // Run the query
		    if ($results = $this->getRecords($fields, $query_parameters, $limit, $offset)) {
			$return = array();
			foreach($results as $row) {
			    $return[] = $this->rowToEntity($row);
			}
			return $return;
		    }
		    
		    return false;
		    
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
		    return \Idno\Core\site()->db();
		}
	    
	}