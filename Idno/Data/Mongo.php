<?php

    /**
     * MongoDB back-end for Known data.
     *
     * This is a wrapper for DataConcierge, but begins to move mongo specific settings
     * to its own class.
     *
     * @package idno
     * @subpackage data
     */

    namespace Idno\Data {

        /**
         * Mongo DB support.
         * @deprecated MongoDB support is being phased out, please use MySQL.
         */
        class Mongo extends \Idno\Core\DataConcierge
        {

            /**
             * Escape sequences for sanitizing fields that will be stored in Mongo.
             * Note that % must be first so that it doesn't double-escape previous sequences
             */
            private static $ESCAPE_SEQUENCES = ['%' => '%25', '$' => '%24', '.' => '%2E'];
            private $dbstring;
            private $dbauthsrc;
            private $dbname;
            private $dbuser;
            private $dbpass;

            function __construct($dbstring = null, $dbuser = null, $dbpass = null, $dbname = null, $dbauthsrc = null)
            {

                $this->dbstring  = $dbstring;
                $this->dbuser    = $dbuser;
                $this->dbpass    = $dbpass;
                $this->dbname    = $dbname;
                $this->dbauthsrc = $dbauthsrc;

                if (empty($dbstring)) {
                    $this->dbstring = \Idno\Core\Idno::site()->config()->dbstring;
                }
                if (empty($dbuser)) {
                    $this->dbuser = \Idno\Core\Idno::site()->config()->dbuser;
                }
                if (empty($dbpass)) {
                    $this->dbpass = \Idno\Core\Idno::site()->config()->dbpass;
                }
                if (empty($dbname)) {
                    $this->dbname = \Idno\Core\Idno::site()->config()->dbname;
                }
                if (empty($dbauthsrc)) {
                    $this->dbauthsrc = \Idno\Core\Idno::site()->config()->dbauthsrc;
                }
                parent::__construct();
            }

            function init()
            {
                try {
                    $this->client = new \MongoClient($this->dbstring, array_filter([
                        'authSource' => $this->dbauthsrc,
                        'username'   => $this->dbuser,
                        'password'   => $this->dbpass,
                    ]));
                } catch (\MongoConnectionException $e) {
                    http_response_code(500);
                    echo '<p>Unfortunately we couldn\'t connect to the database:</p><p>' . $e->getMessage() . '</p>';
                    exit;
                }

                $this->database = $this->client->selectDB($this->dbname);
            }
            
            /**
             * Handle event hooks.
             */
            function registerEventHooks() {
                parent::registerEventHooks();
                
                \Idno\Core\Idno::site()->addEventHook('upgrade', function (\Idno\Core\Event $event) {
                    
                    $new_version = $event->data()['new_version'];
                    $last_update = $event->data()['last_update'];
                    
                    if ($last_update < 2016042301) {
                        
                        \Idno\Core\Idno::site()->logging()->debug("Mongo: Applying mongo upgrades - adding index.");
                        $this->database->entities->createIndex(['created' => 1]);
                    }
                });
            }

            /**
             * Offer a session handler for the current session
             */
            function handleSession()
            {
                ini_set('session.gc_probability', 1);

                $sessionHandler = new \Symfony\Component\HttpFoundation\Session\Storage\Handler\MongoDbSessionHandler(\Idno\Core\Idno::site()->db()->getClient(), [
                    'database'   => 'idnosession',
                    'collection' => 'idnosession'
                ]);

                session_set_save_handler($sessionHandler, true);
            }

            /**
             * Saves a record to the specified database collection
             *
             * @param string $collection
             * @param array $array
             * @return MongoID | false
             */
            function saveRecord($collection, $array)
            {
                $collection_obj = $this->database->selectCollection($collection);
                if (empty($array['_id'])) {
                    unset($array['_id']);
                }
                $array = $this->sanitizeFields($array);
                if ($result = $collection_obj->save($array, array('w' => 1))) {
                    if ($result['ok'] == 1) {
                        return $array['_id'];
                    }
                }

                return false;
            }

            /**
             * Make an array safe for storage in Mongo. This means
             * %-escaping all .'s and $'s.
             *
             * @param mixed $obj an array, scalar value, or null
             * @return mixed
             */
            function sanitizeFields($obj)
            {
                if (is_array($obj)) {
                    // TODO maybe avoid unnecessary object churn by only creating a new
                    // array if a key (or nested array) is found that needs encoding.
                    // The vast majority won't.
                    $result = [];
                    foreach ($obj as $k => $v) {
                        $k          = str_replace(array_keys(self::$ESCAPE_SEQUENCES), array_values(self::$ESCAPE_SEQUENCES), $k);
                        $result[$k] = $this->sanitizeFields($v);
                    }

                    return $result;
                } else if ($obj instanceof \Traversable) {
                    // wrap iterator to sanitize lazily
                    return new \Idno\Common\MappingIterator($obj, [$this, 'sanitizeFields']);
                }

                return $obj;
            }

            /**
             * Retrieves a record from the database by its UUID
             *
             * @param string $id
             * @param string $collection The collection to retrieve from (default: entities)
             * @return array
             */
            function getRecordByUUID($uuid, $collection = 'entities')
            {
                $raw = $this->database->$collection->findOne(array("uuid" => $uuid));

                return $this->unsanitizeFields($raw);
            }

            /**
             * Restore an object's fields after removing it from
             * storage.
             *
             * @param mixed $obj an array, scalar value, or null
             * @return mixed
             */
            function unsanitizeFields($obj)
            {
                if (is_array($obj)) {
                    $result = [];
                    foreach ($obj as $k => $v) {
                        $k          = str_replace(array_values(self::$ESCAPE_SEQUENCES), array_keys(self::$ESCAPE_SEQUENCES), $k);
                        $result[$k] = $this->unsanitizeFields($v);
                    }

                    return $result;
                } else if ($obj instanceof \Traversable) {
                    // wrap iterator to unsanitize lazily
                    return new \Idno\Common\MappingIterator($obj, [$this, 'unsanitizeFields']);
                }

                return $obj;
            }

            /**
             * Process the ID appropriately
             * @param $id
             * @return \MongoId
             */
            function processID($id)
            {
                return new \MongoId($id);
            }

            /**
             * Retrieves a record from the database by ID
             *
             * @param string $id
             * @param string $entities The collection name to retrieve from (default: 'entities')
             * @return array
             */
            function getRecord($id, $collection = 'entities')
            {
                $raw = $this->database->$collection->findOne(array("_id" => new \MongoId($id)));

                return $this->unsanitizeFields($raw);
            }

            /**
             * Retrieves ANY record from a collection
             *
             * @param string $collection
             * @return array
             */
            function getAnyRecord($collection = 'entities')
            {
                $raw = $this->database->$collection->findOne();

                return $this->unsanitizeFields($raw);
            }

            /**
             * Retrieve objects of a certain kind that we're allowed to see,
             * (or excluding kinds that we don't want to see),
             * in reverse chronological order
             *
             * @param string|array $subtypes String or array of subtypes we're allowed to see
             * @param array $search Any extra search terms in array format (eg array('foo' => 'bar')) (default: empty)
             * @param array $fields An array of fieldnames to return (leave empty for all; default: all)
             * @param int $limit Maximum number of records to return (default: 10)
             * @param int $offset Number of records to skip (default: 0)
             * @param string $collection Collection to query; default: entities
             * @param array $readGroups Which ACL groups should we check? (default: everything the user can see)
             * @return array|false Array of elements or false, depending on success
             */
            function getObjects($subtypes = '', $search = array(), $fields = array(), $limit = 10, $offset = 0, $collection = 'entities', $readGroups = [])
            {

                // Initialize query parameters to be an empty array
                $query_parameters = array();

                // Ensure subtypes are recorded properly
                // and remove subtypes that have an exclamation mark before them
                // from consideration
                if (!empty($subtypes)) {
                    $not = array();
                    if (!is_array($subtypes)) {
                        $subtypes = array($subtypes);
                    }
                    foreach ($subtypes as $key => $subtype) {
                        if (substr($subtype, 0, 1) == '!') {
                            unset($subtypes[$key]);
                            $not[] = substr($subtype, 1);
                        }
                    }
                    if (!empty($subtypes)) {
                        $query_parameters['entity_subtype']['$in'] = $subtypes;
                    }
                    if (!empty($not)) {
                        $query_parameters['entity_subtype']['$not']['$in'] = $not;
                    }
                }

                // Make sure we're only getting objects that we're allowed to see
                if (empty($readGroups)) {
                    $readGroups = \Idno\Core\Idno::site()->session()->getReadAccessGroupIDs();
                }
                $query_parameters['access'] = array('$in' => $readGroups);

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
                if ($results = $this->getRecords($fields, $query_parameters, $limit, $offset, $collection)) {
                    $return = array();
                    foreach ($results as $row) {
                        $return[] = $this->rowToEntity($row);
                    }

                    return $return;
                }

                return false;
            }

            /**
             * Retrieves a set of records from the database with given parameters, in
             * reverse chronological order
             *
             * @param array $parameters Query parameters in MongoDB format
             * @param int $limit Maximum number of records to return
             * @param int $offset Number of records to skip
             * @param string $collection The collection to interrogate (default: 'entities')
             * @return iterator|false Iterator or false, depending on success
             */
            function getRecords($fields, $parameters, $limit, $offset, $collection = 'entities')
            {
                try {
                    // Make search case insensitive
                    $fieldscopy = $fields;
                    foreach ($fields as $key => $value) {
                        if (is_string($value)) {
                            $val              = new \MongoRegex("/{$value}/i");
                            $fieldscopy[$key] = $val;
                        }
                    }
                    $fields = $fieldscopy;
                    $result = $this->database->$collection
                        ->find($parameters, $fields)
                        ->skip($offset)
                        ->limit($limit)
                        ->sort(array('created' => -1));
                    
                    if (($result) && (count(iterator_to_array($result))) ) {
                        return $this->unsanitizeFields($result);
                    }
                } catch (\Exception $e) {
                    return false;
                }

                return false;
            }

            /**
             * Export a collection to JSON.
             * @param string $collection
             * @return bool|string
             */
            function exportRecords($collection = 'entities')
            {
                try {
                    if ($result = $this->database->$collection->find()) {
                        $result = $this->unsanitizeFields($result);

                        return json_encode(iterator_to_array($result));
                    }
                } catch (\Exception $e) {
                    return false;
                }

                return false;
            }

            /**
             * Count objects of a certain kind that we're allowed to see
             *
             * @param string|array $subtypes String or array of subtypes we're allowed to see
             * @param array $search Any extra search terms in array format (eg array('foo' => 'bar')) (default: empty)
             * @param string $collection Collection to query; default: entities
             */
            function countObjects($subtypes = '', $search = array(), $collection = 'entities')
            {

                // Initialize query parameters to be an empty array
                $query_parameters = array();

                // Ensure subtypes are recorded properly
                // and remove subtypes that have an exclamation mark before them
                // from consideration
                if (!empty($subtypes)) {
                    $not = array();
                    if (!is_array($subtypes)) {
                        $subtypes = array($subtypes);
                    }
                    foreach ($subtypes as $key => $subtype) {
                        if (substr($subtype, 0, 1) == '!') {
                            unset($subtypes[$key]);
                            $not[] = substr($subtype, 1);
                        }
                    }
                    if (!empty($subtypes)) {
                        $query_parameters['entity_subtype']['$in'] = $subtypes;
                    }
                    if (!empty($not)) {
                        $query_parameters['entity_subtype']['$not']['$in'] = $not;
                    }
                }

                // Make sure we're only getting objects that we're allowed to see
                $readGroups                 = \Idno\Core\Idno::site()->session()->getReadAccessGroupIDs();
                $query_parameters['access'] = array('$in' => $readGroups);

                // Join the rest of the search query elements to this search
                $query_parameters = array_merge($query_parameters, $search);

                return $this->countRecords($query_parameters, $collection);
            }

            /**
             * Count the number of records that match the given parameters
             * @param array $parameters
             * @param string $collection The collection to interrogate (default: 'entities')
             * @return int
             */
            function countRecords($parameters, $collection = 'entities')
            {
                if ($result = $this->database->$collection->count($parameters)) {
                    return (int)$result;
                }

                return 0;
            }

            /**
             * Remove an entity from the database
             * @param string $id
             * @return true|false
             */
            function deleteRecord($id, $collection = 'entities')
            {
                return $this->database->$collection->remove(array("_id" => new \MongoId($id)));
            }

            /**
             * Retrieve the filesystem associated with the current db, suitable for saving
             * and retrieving files
             * @return bool|\MongoGridFS
             */
            function getFilesystem()
            {
                if ($grid = new \MongoGridFS($this->database)) {
                    return $grid;
                }

                return false;
            }

            /**
             * Given a text query, return an array suitable for adding into getFromX calls
             * @param $query
             * @return array
             */
            function createSearchArray($query)
            {
                $regexObj = new \MongoRegex("/" . addslashes($query) . "/i");

                return array('$or' => array(array('body' => $regexObj), array('title' => $regexObj), array('tags' => $regexObj), array('description' => $regexObj)));
            }

        }

    }
