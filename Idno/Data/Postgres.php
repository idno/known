<?php

    /**
     * Postgres back-end for Known data.
     *
     * @package idno
     * @subpackage data
     */

    namespace Idno\Data {

        class Postgres extends \Idno\Core\DataConcierge
        {
            private $dbname;
            private $dbuser;
            private $dbpass;
            private $dbhost;
            private $dbport;

            function __construct($dbuser = null, $dbpass = null, $dbname = null, $dbhost = null, $dbport = null)
            {

                $this->dbuser = $dbuser;
                $this->dbpass = $dbpass;
                $this->dbname = $dbname;
                $this->dbhost = $dbhost;
                $this->dbport = $dbport;

                if (empty($dbuser)) {
                    $this->dbuser = \Idno\Core\Idno::site()->config()->dbuser;
                }
                if (empty($dbpass)) {
                    $this->dbpass = \Idno\Core\Idno::site()->config()->dbpass;
                }
                if (empty($dbname)) {
                    $this->dbname = \Idno\Core\Idno::site()->config()->dbname;
                }
                if (empty($dbhost)) {
                    $this->dbhost = \Idno\Core\Idno::site()->config()->dbhost;
                }
                if (empty($dbport)) {
                    $this->dbport = \Idno\Core\Idno::site()->config()->dbport;
                }

                parent::__construct();
            }

            function init()
            {

                try {
                    $connection_string = 'pgsql:dbname=' . $this->dbname;
                    if (!empty($this->dbhost)) {
                        $connection_string .= ';host=' . $this->dbhost;
                    }
                    if (!empty($this->dbport)) {
                        $connection_string .= ';port=' . $this->dbport;
                    }
                    if (!empty($this->dbuser)) {
                        $connection_string .= ';user=' . $this->dbuser;
                    }
                    if (!empty($this->dbpass)) {
                        $connection_string .= ';password=' . $this->dbpass;
                    }
                    $this->client = new \PDO($connection_string);
                    $this->client->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                } catch (\Exception $e) {
                    error_log($e->getMessage());
                    if (!empty(\Idno\Core\Idno::site()->config()->forward_on_empty)) {
                        header('Location: ' . \Idno\Core\Idno::site()->config()->forward_on_empty);
                        exit;
                    } else {

                        http_response_code(500);

                        if (\Idno\Core\Idno::site()->config()->debug) {
                            $message = '<p>' . $e->getMessage() . '</p>';
                            $message .= '<p>' . $connection_string . '</p>';
                        }
                        error_log($e->getMessage());
                        include \Idno\Core\Idno::site()->config()->path . '/statics/db.php';
                        exit;
                    }
                }

                $this->database = $this->dbname;
                $this->checkAndUpgradeSchema();

            }

            /**
             * Checks the current schema version and upgrades if necessary
             */
            function checkAndUpgradeSchema()
            {
                if ($versions = $this->getVersions()) {
                    foreach ($versions as $version) {
                        if ($version->label == 'schema') {
                            $basedate          = $newdate = (int)$version->value;
                            $upgrade_sql_files = array();
                            $schema_dir        = dirname(dirname(dirname(__FILE__))) . '/schemas/mysql/';
                            $client            = $this->client;

                        }
                    }
                }
            }

            /**
             * Retrieve version information from the schema
             * @return array|bool
             */
            function getVersions()
            {
                try {
                    $client = $this->client;
                    /* @var \PDO $client */
                    $statement = $client->prepare("select * from versions");
                    if ($statement->execute()) {
                        return $statement->fetchAll(\PDO::FETCH_OBJ);
                    }
                } catch (\Exception $e) {
                    //\Idno\Core\Idno::site()->logging()->log($e->getMessage());
                    error_log($e->getMessage());
                }

                return false;
            }

            /**
             * Handle the session in Postgres
             */
            function handleSession()
            {
                if (version_compare(phpversion(), '5.3', '>')) {
                    $sessionHandler = new \Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler(\Idno\Core\Idno::site()->db()->getClient(),
                        array(
                            'db_table'    => 'session',
                            'db_id_col'   => 'session_id',
                            'db_data_col' => 'session_value',
                            'db_time_col' => 'session_time',
                        )
                    );

                    session_set_save_handler($sessionHandler, true);
                }
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
             * MySQL doesn't need the ID to be processed.
             * @param $id
             * @return string
             */
            function processID($id)
            {
                return $id;
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
                $collection = $this->sanitiseCollection($collection);

                if (empty($array['_id'])) {
                    $array['_id'] = md5(rand() . microtime(true));
                }
                if (empty($array['uuid'])) {
                    $array['uuid'] = \Idno\Core\Idno::site()->config()->getURL() . 'view/' . $array['_id'];
                }
                if (empty($array['owner'])) {
                    $array['owner'] = '';
                }
                try {
                    $contents = json_encode($array);
                } catch (\Exception $e) {
                    $contents = json_encode([]);
                    \Idno\Core\Idno::site()->logging()->log($e->getMessage());

                    return false;
                }
                $search = '';
                if (!empty($array['title'])) {
                    $search .= $array['title'] . ' ';
                }
                if (!empty($array['tags'])) {
                    $search .= $array['tags'] . ' ';
                }
                if (!empty($array['description'])) {
                    $search .= $array['description'] . ' ';
                }
                if (!empty($array['body'])) {
                    $search .= strip_tags($array['body']);
                }
                if (empty($array['entity_subtype'])) {
                    $array['entity_subtype'] = 'Idno\\Common\\Entity';
                }
                if (empty($array['created'])) {
                    $array['created'] = date("Y-m-d H:i:s", time());
                } else {
                    $array['created'] = date("Y-m-d H:i:s", $array['created']);
                }

                $search = str_replace("\n", " \n ", $search);
                $search = str_replace("\r", "", $search);
                $search = str_replace("#", " #", $search);
                $search = strtolower($search);

                $client = $this->client;
                /* @var \PDO $client */

                try {
                    // crazy Postgres equivalent of MySQL's "on duplicate update" using
                    // Common Table Expressions ("with...") http://www.the-art-of-web.com/sql/upsert/#section_1
                    // Postgres 9.5 will have "on conflict do update"
                    $upsert = "update {$collection}
                               set uuid=:uuid, entity_subtype=:subtype, owner=:owner,
                               contents=:contents, search=:search where _id=:id";
                    $insert = "insert into {$collection}
                               (uuid, _id, owner, entity_subtype, contents, search)
                               select :uuid, :id, :owner, :subtype, :contents, :search";

                    $statement = $client->prepare("with upsert as (${upsert} returning *)
                                                   ${insert} where not exists (select * from upsert)");

                    if ($statement->execute(array(':uuid' => $array['uuid'], ':id' => $array['_id'], ':owner' => $array['owner'], ':subtype' => $array['entity_subtype'], ':contents' => $contents, ':search' => $search))) {
                        if ($statement = $client->prepare("delete from metadata where _id = :id")) {
                            $statement->execute(array(':id' => $array['_id']));
                        }
                        foreach ($array as $key => $val) {
                            if (!is_array($val)) {
                                $val = array($val);
                            }
                            foreach ($val as $value) {
                                if (is_array($value) || is_object($value)) {
                                    try {
                                        $value = json_encode($value);
                                    } catch (\Exception $e) {
                                        $value = json_encode([]);
                                        \Idno\Core\Idno::site()->logging()->log($e->getMessage());
                                    }
                                }
                                if (empty($value)) {
                                    $value = 0;
                                }
                                if ($statement = $client->prepare("insert into metadata (collection, entity, _id, name, value) values (:collection, :uuid, :id::text, :name, :value)")) {
                                    $statement->execute(array('collection' => $collection, ':uuid' => $array['uuid'], ':id' => $array['_id'], ':name' => $key, ':value' => $value));
                                }
                            }
                        }

                        return $array['_id'];
                    }
                } catch (\Exception $e) {
                    error_log($e->getMessage());
                    //\Idno\Core\Idno::site()->logging()->log($e->getMessage());
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
                try {
                    $collection = $this->sanitiseCollection($collection);

                    $statement = $this->client->prepare("select distinct {$collection}.* from " . $collection . " where uuid = :uuid");
                    if ($statement->execute(array(':uuid' => $uuid))) {
                        if ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                            return json_decode($row['contents'], true);
                        }
                    }
                } catch (\Exception $e) {
                    \Idno\Core\Idno::site()->logging()->log($e->getMessage());
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
                $collection = $this->sanitiseCollection($collection);

                $statement = $this->client->prepare("select {$collection}.* from " . $collection . " where _id = :id");
                if ($statement->execute(array(':id' => $id))) {
                    if ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                        return json_decode($row['contents'], true);
                    }
                }

                return false;
            }

            /**
             * Retrieves ANY record from a collection
             *
             * @param string $collection
             * @return array
             */
            function getAnyRecord($collection = 'entities')
            {
                try {
                    $collection = $this->sanitiseCollection($collection);

                    $statement = $this->client->prepare("select {$collection}.* from " . $collection . " limit 1");
                    if ($statement->execute()) {
                        if ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                            return json_decode($row['contents'], true);
                        }
                    }
                } catch (\Exception $e) {
                    if (\Idno\Core\Idno::site()->session() == null)
                        die($e->getMessage());
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
                        $query_parameters['entity_subtype']['$not'] = $not;
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

                return array();

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
                    $collection = $this->sanitiseCollection($collection);

                    // Build query
                    $query            = "select distinct {$collection}.* from {$collection} ";
                    $variables        = array();
                    $metadata_joins   = 0;
                    $non_md_variables = array();
                    $limit            = (int)$limit;
                    $offset           = (int)$offset;
                    $where            = $this->build_where_from_array($parameters, $variables, $metadata_joins, $non_md_variables, 'and', $collection);
                    for ($i = 1; $i <= $metadata_joins; $i++) {
                        $query .= " left join metadata md{$i} on md{$i}.entity = {$collection}.uuid ";
                    }
                    if (!empty($where)) {
                        $query .= ' where ' . $where . ' ';
                    }
                    $query .= " order by {$collection}.created desc limit {$limit} offset {$offset}";

                    $client = $this->client;
                    /* @var \PDO $client */

                    $statement = $client->prepare($query);

//                    error_log(str_replace(array_keys($variables), array_values($variables), $query));

                    if ($statement->execute($variables)) {
                        if ($rows = $statement->fetchAll(\PDO::FETCH_ASSOC)) {
                            $records = [];
                            foreach ($rows as $row) {
                                $records[] = json_decode($row['contents'], true);
                            }

                            return $records;
                        }
                    }

                } catch (\Exception $e) {
                    \Idno\Core\Idno::site()->logging()->log($e->getMessage());

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
            function build_where_from_array($params, &$variables, &$metadata_joins, &$non_md_variables, $clause = 'and', $collection = 'entities')
            {

                $where = '';
                if (empty($variables)) {
                    $variables = array();
                }
                if (empty($metadata_joins)) {
                    $metadata_joins = 0;
                }
                if (empty($non_md_variables)) {
                    $non_md_variables = 0;
                }
                if (is_array($params) && !empty($params)) {
                    $subwhere = array();
                    foreach ($params as $key => $value) {
                        if (!is_array($value)) {
                            if (in_array($key, array('uuid', '_id', 'entity_subtype', 'owner', 'created'))) {
                                $subwhere[] = "({$collection}.{$key} = :nonmdvalue{$non_md_variables})";
                                if ($key == 'created') {
                                    if (!is_int($value)) {
                                        $value = strtotime($value);
                                    }
                                }
                                $variables[":nonmdvalue{$non_md_variables}"] = $value;
                                $non_md_variables++;
                            } else {
                                $metadata_joins++;
                                $subwhere[]                           = "(md{$metadata_joins}.name = :name{$metadata_joins} and md{$metadata_joins}.value = :value{$metadata_joins} and md{$metadata_joins}.collection = '{$collection}')";
                                $variables[":name{$metadata_joins}"]  = $key;
                                $variables[":value{$metadata_joins}"] = $value;
                            }
                        } else {
                            if (!empty($value['$or'])) {
                                $subwhere[] = "(" . $this->build_where_from_array($value['$or'], $variables, $metadata_joins, $non_md_variables, 'or', $collection) . ")";
                            }
                            if (!empty($value['$not'])) {
                                if (!empty($value['$not']['$in'])) {
                                    $value['$not'] = array_merge($value['$not'], $value['$not']['$in']);
                                    unset($value['$not']['$in']);
                                }
                                if (in_array($key, array('uuid', '_id', 'entity_subtype', 'owner'))) {
                                    $notstring = "{$collection}.$key not in(";
                                    $i         = 0;
                                    foreach ($value['$not'] as $val) {
                                        if ($i > 0) $notstring .= ', ';
                                        $notstring .= ":nonmdvalue{$non_md_variables}";
                                        $variables[":nonmdvalue{$non_md_variables}"] = $val;
                                        $non_md_variables++;
                                        $i++;
                                    }
                                    $notstring .= ")";
                                } else {
                                    $metadata_joins++;
                                    $notstring                           = "(md{$metadata_joins}.name = :name{$metadata_joins} and md{$metadata_joins}.collection = '{$collection}' and md{$metadata_joins}.value not in (";
                                    $variables[":name{$metadata_joins}"] = $key;
                                    $i                                   = 0;
                                    foreach ($value['$not'] as $val) {
                                        if ($i > 0) $notstring .= ', ';
                                        $notstring .= ":nonmdvalue{$non_md_variables}";
                                        $variables[":nonmdvalue{$non_md_variables}"] = $val;
                                        $non_md_variables++;
                                        $i++;
                                    }
                                    $notstring .= "))";
                                }
                                $subwhere[] = $notstring;
                            }
                            if (!empty($value['$in'])) {
                                if (in_array($key, array('uuid', '_id', 'entity_subtype', 'owner'))) {
                                    $instring = "{$collection}.$key in (";
                                    $i        = 0;
                                    foreach ($value['$in'] as $val) {
                                        if ($i > 0) $instring .= ', ';
                                        $instring .= ":nonmdvalue{$non_md_variables}";
                                        $variables[":nonmdvalue{$non_md_variables}"] = $val;
                                        $non_md_variables++;
                                        $i++;
                                    }
                                    $instring .= ")";
                                } else {
                                    $metadata_joins++;
                                    $instring                            = "(md{$metadata_joins}.name = :name{$metadata_joins} and md{$metadata_joins}.collection = '{$collection}' and md{$metadata_joins}.value in (";
                                    $variables[":name{$metadata_joins}"] = $key;
                                    $i                                   = 0;
                                    foreach ($value['$in'] as $val) {
                                        if ($i > 0) $instring .= ', ';
                                        $instring .= ":nonmdvalue{$non_md_variables}";
                                        $variables[":nonmdvalue{$non_md_variables}"] = $val;
                                        $non_md_variables++;
                                        $i++;
                                    }
                                    $instring .= "))";
                                }
                                $subwhere[] = $instring;
                            }
                            if ($key == '$search' && !empty($value)) {
                                $val = $value[0]; // The search query is always in $value position [0] for now
//                                if (strlen($val) > 5) {
//                                    $subwhere[]                                  = "match (search) against (:nonmdvalue{$non_md_variables})";
//                                    $variables[":nonmdvalue{$non_md_variables}"] = $val;
//                                } else {
                                $subwhere[]                                  = "search like :nonmdvalue{$non_md_variables}";
                                $variables[":nonmdvalue{$non_md_variables}"] = '%' . $val . '%';
//                                }
                                $non_md_variables++;
                            }
                        }
                    }
                    if (!empty($subwhere)) {
                        $where = '(' . implode(" {$clause} ", $subwhere) . ')';
                    }
                }

                return $where;
            }

            /**
             * Export a collection as SQL.
             * @param string $collection
             * @return bool|string
             */
            function exportRecords($collection = 'entities')
            {
                // TODO
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
                        $query_parameters['entity_subtype']['$not'] = $not;
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
                try {

                    $collection = $this->sanitiseCollection($collection);

                    // Build query
                    $query            = "select count(distinct {$collection}.uuid) as total from {$collection} ";
                    $variables        = array();
                    $metadata_joins   = 0;
                    $non_md_variables = array();
                    $where            = $this->build_where_from_array($parameters, $variables, $metadata_joins, $non_md_variables, 'and', $collection);
                    for ($i = 0; $i <= $metadata_joins; $i++) {
                        $query .= " left join metadata md{$i} on md{$i}.entity = {$collection}.uuid ";
                    }
                    if (!empty($where)) {
                        $query .= ' where ' . $where . ' ';
                    }

                    $client = $this->client;
                    /* @var \PDO $client */
                    $statement = $client->prepare($query);
                    if ($result = $statement->execute($variables)) {
                        if ($obj = $statement->fetchObject()) {
                            return (int)$obj->total;
                        }
                    }

                } catch (Exception $e) {
                    \Idno\Core\Idno::site()->logging()->log($e->getMessage());

                    return false;
                }

                return 0;
            }

            /**
             * Get database errors
             * @return mixed
             */
            function getErrors()
            {
                if (!empty($this->client)) {
                    return $this->client->errorInfo();
                }

                return false;
            }

            /**
             * Remove an entity from the database
             * @param string $id
             * @return true|false
             */
            function deleteRecord($id, $collection = 'entities')
            {
                try {

                    $collection = $this->sanitiseCollection($collection);

                    $client = $this->client;
                    /* @var \PDO $client */
                    $statement = $client->prepare("delete from {$collection} where _id = :id");
                    if ($statement->execute(array(':id' => $id))) {
                        if ($statement = $client->prepare("delete from metadata where _id = :id")) {
                            return $statement->execute(array(':id' => $id));
                        }
                    }

                } catch (\Exception $e) {

                    \Idno\Core\Idno::site()->logging()->log($e->getMessage());

                    return false;

                }

                return false;
            }

            /**
             * Retrieve the filesystem associated with the current db, suitable for saving
             * and retrieving files
             * @return bool
             */
            function getFilesystem()
            {
                // We're not returning a filesystem for MySQL
                return false;
            }

            /**
             * Given a text query, return an array suitable for adding into getFromX calls
             * @param $query
             * @return array
             */
            function createSearchArray($query)
            {
                return array('$search' => array($query));
            }

        }

        /**
         * Helper function that returns the current database object
         * @return \Idno\Core\DataConcierge
         */
        function db()
        {
            return \Idno\Core\Idno::site()->db();
        }

    }
