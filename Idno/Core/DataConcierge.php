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

        class DataConcierge extends \Idno\Common\Component
        {

            private $client = null;
            private $database = null;

            function init()
            {
                try {
                    $this->client = new \MongoClient(site()->config()->dbstring);
                } catch (\MongoConnectionException $e) {
                    echo '<p>Unfortunately we couldn\'t connect to the database:</p><p>' . $e->getMessage() . '</p>';
                    exit;
                }

                $this->database = $this->client->selectDB(site()->config()->dbname);
            }

            /**
             * Returns an instance of the database reference variable
             * @return \MongoDB
             */
            function getDatabase()
            {
                return $this->database;
            }

            /**
             * Returns an instance of the database client reference variable
             * @return \Mongo
             */
            function getClient()
            {
                return $this->client;
            }

            /**
             * Offer a session handler for the current session
             */
            function handleSession()
            {
                session_save_path(site()->config()->session_path);
                ini_set('session.gc_probability', 1);
                
                /*$sessionHandler = new \Symfony\Component\HttpFoundation\Session\Storage\Handler\MongoDbSessionHandler(\Idno\Core\site()->db()->getClient(), [
                    'database'   => 'idnosession',
                    'collection' => 'idnosession'
                ]);

                session_set_save_handler($sessionHandler, true);*/
            }

            /**
             * Saves an idno entity to the database, returning the _id
             * field on success.
             *
             * @param Entity $object
             */

            function saveObject($object)
            {
                if ($object instanceof \Idno\Common\Entity) {
                    if ($collection = $object->getCollection()) {
                        $array = $object->saveToArray();
                        return $this->saveRecord($collection, $array);
                    }
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

            function saveRecord($collection, $array)
            {
                $collection_obj = $this->database->selectCollection($collection);
                if (empty($array['_id'])) {
                    unset($array['_id']);
                }
                if ($result = $collection_obj->save($array, array('w' => 1))) {
                    if ($result['ok'] == 1) {
                        return $array['_id'];
                    }
                }

                return false;
            }

            /**
             * Retrieves an Idno entity object by its UUID, casting it to the
             * correct class
             *
             * @param string $id
             * @return \Idno\Common\Entity | false
             */

            function getObject($uuid)
            {
                if ($result = $this->getRecordByUUID($uuid)) {
                    if ($object = $this->rowToEntity($result)) {
                        if ($object->canRead()) {
                            return $object;
                        }
                    }
                }

                return false;
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
             * Retrieves a record from the database by its UUID
             *
             * @param string $id
             * @param string $collection The collection to retrieve from (default: entities)
             * @return array
             */

            function getRecordByUUID($uuid, $collection = 'entities')
            {
                return $this->database->$collection->findOne(array("uuid" => $uuid));
            }

            /**
             * Converts a database row into an Idno entity
             *
             * @param array $row
             * @return \Idno\Common\Entity
             */
            function rowToEntity($row)
            {
                if (!empty($row['entity_subtype']))
                    if (class_exists($row['entity_subtype'])) {
                        $object = new $row['entity_subtype']();
                        $object->loadFromArray($row);

                        return $object;
                    }

                return false;
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
                return $this->database->$collection->findOne(array("_id" => new \MongoId($id)));
            }

            /**
             * Retrieves ANY record from a collection
             *
             * @param string $collection
             * @return mixed
             */
            function getAnyRecord($collection = 'entities')
            {
                return $this->database->$collection->findOne();
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
             * @return array|false Array of elements or false, depending on success
             */

            function getObjects($subtypes = '', $search = array(), $fields = array(), $limit = 10, $offset = 0, $collection = 'entities')
            {

                // Initialize query parameters to be an empty array
                $query_parameters = array();

                // Ensure subtypes are recorded properly
                // and remove subtypes that have an exclamation mark before them
                // from consideration
                if (!empty($subtypes)) {
                    $not = [];
                    if (!is_array($subtypes)) {
                        $subtypes = [$subtypes];
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
                $readGroups                 = site()->session()->getReadAccessGroupIDs();
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
                    if ($result = $this->database->$collection->find($parameters, $fields)->skip($offset)->limit($limit)->sort(array('created' => -1))) {
                        return $result;
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
            function countObjects($subtypes = '', $search = [], $collection = 'entities')
            {

                // Initialize query parameters to be an empty array
                $query_parameters = array();

                // Ensure subtypes are recorded properly
                // and remove subtypes that have an exclamation mark before them
                // from consideration
                if (!empty($subtypes)) {
                    $not = [];
                    if (!is_array($subtypes)) {
                        $subtypes = [$subtypes];
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
                $readGroups                 = site()->session()->getReadAccessGroupIDs();
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

                return ['$or' => [['body' => $regexObj], ['title' => $regexObj], ['tags' => $regexObj], ['description' => $regexObj]]];
            }

        }

        /**
         * Helper function that returns the current database object
         * @return \Idno\Core\DataConcierge
         */
        function db()
        {
            return \Idno\Core\site()->db();
        }

    }
