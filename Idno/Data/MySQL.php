<?php

    /**
     * Known Data handling for MySQL.
     *
     * THIS IS A WORK IN PROGRESS AND SHOULD NOT (CANNOT!) BE USED IN PRODUCTION
     *
     * Once this is complete, you will be able to set \Idno\Core\Idno->$db to an
     * instance of this class to use MySQL.
     *
     * @package idno
     * @subpackage data
     */

    namespace Idno\Data {

        class MySQL extends \Idno\Core\DataConcierge
        {

            private $client = null;
            private $database = null;

            function init()
            {

                try {
                    $this->client = new \PDO('mysql:host='.\Idno\Core\site()->config()->dbhost.';dbname='.\Idno\Core\site()->config()->dbname.';charset=utf8', \Idno\Core\site()->config()->dbuser, \Idno\Core\site()->config()->dbpass);
                } catch (\Exception $e) {
                    \Idno\Core\site()->session()->addMessage('Unfortunately we couldn\'t connect to the database: ' . $e->getMessage());
                }

                $this->database = \Idno\Core\site()->config()->dbname;

            }

            /**
             * Returns an instance of the database reference variable
             * @return string;
             */
            function getDatabase()
            {
                return $this->database;
            }

            /**
             * Returns an instance of the database client reference variable
             * @return \PDO
             */
            function getClient()
            {
                return $this->client;
            }

            /**
             * Saves a Known entity to the database, returning the _id
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
             * @return int | false
             */

            function saveRecord($collection, $array)
            {
                /*
                $collection_obj = $this->database->selectCollection($collection);
                if ($result = $collection_obj->save($array, array('w' => 1))) {
                    if ($result['ok'] == 1) {
                        return $array['_id'];
                    }
                }*/

                if (empty($array['_id'])) {
                    $array['_id'] = md5(rand(0,9999) . time());
                }
                if (empty($array['uuid'])) {
                    $array['uuid'] = \Idno\Core\site()->config()->getURL() . 'view/' . $array['_id'];
                }
                if (empty($array['owner'])) {
                    $array['owner'] = '';
                }
                $contents = json_encode($array);
                $search = '';
                if (!empty($array['title'])) {
                    $search .= $array['title'] . ' ';
                }
                if (!empty($array['description'])) {
                    $search .= $array['description'] . ' ';
                }
                if (!empty($array['body'])) {
                    $search .= strip_tags($array['body']);
                }

                $client = $this->client;
                /* @var \PDO $client */
                $statement = $client->prepare("insert into {$collection} (`uuid`,`_id`, `owner`, `contents`, `search`) values (:uuid, :id, :owner, :contents, :search)");
                if ($statement->execute([':uuid' => $array['uuid'], ':id' => $array['_id'], ':owner' => $array['owner'], ':contents' => $contents, ':search' => $search])) {
                    if ($statement = $client->prepare("delete from metadata where entity = :uuid")) {
                        $statement->execute([':uuid' => $array['uuid']]);
                    }
                    foreach($array as $key => $value) {
                        if ($statement = $client->prepare("insert into metadata set entity = :uuid, `name` = :name, `value` = :value")) {
                            $statement->execute([':uuid' => $array['uuid'], ':name' => $key, ':value' => $value]);
                        }
                    }
                    return $array['_id'];
                }

                return false;
            }

            /**
             * Retrieves a Known entity object by its UUID, casting it to the
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
             * Retrieves a record from the database by its UUID
             *
             * @param string $id
             * @param string $collection The collection to retrieve from (default: entities)
             * @return array
             */

            function getRecordByUUID($uuid, $collection = 'entities')
            {
                $statement = $this->client->prepare("select * from ".$collection." where uuid = :uuid");
                if($statement->execute([':uuid' => $uuid])) {
                    return $statement->fetch(\PDO::FETCH_OBJ);
                }
                return false;
            }

            /**
             * Converts a database row into a Known entity
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
                $statement = $this->client->prepare("select * from ".$collection." where _id = :id");
                if ($statement->execute([':id' => $id])) {
                    return $statement->fetch(\PDO::FETCH_OBJ);
                }
                return false;
            }

            /**
             * Retrieves ANY record from a collection
             *
             * @param string $collection
             * @return mixed
             */
            function getAnyRecord($collection = 'entities')
            {
                $statement = $this->client->prepare("select * from " . $collection . " limit 1");
                if($statement->execute()) {
                    return $statement->fetch(\PDO::FETCH_OBJ);
                }
                return false;
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

                    // Build query
                    $query = "select * from entities ";
                    $variables = [];
                    $metadata_joins = 0;
                    $limit = (int) $limit;
                    $offset = (int) $offset;
                    $where = $this->build_where_from_array($parameters, $variables, $metadata_joins);
                    for ($i = 0; $i < $metadata_joins; $i++) {
                        $query .= " left_join metadata md{$i} on md{$i}.entity = entities.uuid ";
                    }
                    if (!empty($where)) {
                        $query .= ' where ' . $where . ' ';
                    }
                    $query .= " order by entities.`created` desc limit {$offset},{$limit}";

                    $client = $this->client; /* @var \PDO $client */
                    $statement = $client->prepare($query);
                    if ($result = $statement->execute($variables)) {
                        return $statement->fetchAll(\PDO::FETCH_OBJ);
                    }

                } catch (Exception $e) {
                    return false;
                }

                return false;
            }

            /**
             * Recursive function that takes an array of parameters and returns an array of clauses suitable
             * for compiling into an SQL query
             * @param $params
             * @param $where
             * @param $variables
             * @param $metadata_joins
             * @param $non_md_variables
             * @param string $clause Defaults to 'and'
             */
            function build_where_from_array($params, &$variables, &$metadata_joins, &$non_md_variables, $clause = 'and') {
                $where = '';
                if (empty($variables)) {
                    $variables = [];
                }
                if (empty($metadata_joins)) {
                    $metadata_joins = 0;
                }
                if (empty($non_md_variables)) {
                    $non_md_variables = 0;
                }
                if (is_array($params) && !empty($params)) {
                    $subwhere = [];
                    foreach($params as $key => $value) {
                        if (!is_array($value)) {
                            if (in_array($key, ['uuid','_id','entity_subtype','owner'])) {
                                $subwhere[] = "(`entities`.`{$key}`) = :nonmdvalue{$non_md_variables}";
                                $variables[":nonmdvalue{$non_md_variables}"];
                                $non_md_variables++;
                            } else {
                                $subwhere[] = "(md{$metadata_joins}.`name` = :name{$metadata_joins} and md{$metadata_joins}.`value` = :value{$metadata_joins})";
                                $variables[":name{$metadata_joins}"] = $key;
                                $variables[":value{$metadata_joins}"] = $value;
                                $metadata_joins++;
                            }
                        } else if ($key == '$or') {
                            $subwhere[] = "(". $this->build_where_from_array($value, $variables, $metadata_joins, $non_md_variables, 'or') .")";
                        } else if ($key == '$not') {
                            $notstring = "(md{$metadata_joins}.`name` = :name{$metadata_joins} and md{$metadata_joins}.`name` not in (";
                            foreach($value as $val) {
                                $notstring .= ":value{$metadata_joins}";
                                $variables[":value{$metadata_joins}"] = $val;
                                $metadata_joins++;
                            }
                            $notstring .= "))";
                            $subwhere[] = $notstring;
                        } else if ($key == '$search') {
                            $val = $value[0]; // The search query is always in $value position [0] for now
                            $subwhere[] = "match (entities.`search`) against (:nonmdvalue{$non_md_variables})";
                            $variables[":nonmdvalue{$non_md_variables}"] = $val;
                            $non_md_variables++;
                        }
                    }
                    if (!empty($subwhere)) {
                        $where = '(' . implode(" {$clause} ", $subwhere) . ')';
                    }
                }
                return $where;
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
                        $query_parameters['entity_subtype']['$not'] = $not;
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
                /*
                 * TODO MySQL select query
                 *
                if ($result = $this->database->$collection->count($parameters)) {
                    return (int)$result;
                }*/

                return 0;
            }

            /**
             * Remove an entity from the database
             * @param string $id
             * @return true|false
             */
            function deleteRecord($id)
            {
                // TODO MySQL query
                //return $this->database->entities->remove(array("_id" => new \MongoId($id)));
            }

            /**
             * Retrieve the filesystem associated with the current db, suitable for saving
             * and retrieving files
             * @return bool
             */
            function getFilesystem()
            {
                /*
                 * TODO local file system
                if ($grid = new \MongoGridFS($this->database)) {
                    return $grid;
                }
                */

                return false;
            }

            /**
             * Given a text query, return an array suitable for adding into getFromX calls
             * @param $query
             * @return array
             */
            function createSearchArray($query)
            {
                //$regexObj = new \MongoRegex("/" . addslashes($query) . "/i");
                return ['$search' => [$query]];
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