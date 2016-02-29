<?php

    /**
     * SQLite3 back-end for Known data.
     *
     * @requires php5-sqlite
     * @package idno
     * @subpackage data
     */

    namespace Idno\Data {

        class Sqlite3 extends AbstractSQL
        {

            function init()
            {

                try {

                    $connection_string = "sqlite:" . $this->dbname;
                    $this->client      = new \PDO($connection_string);
                    $this->client->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                    $this->client->exec("SELECT * from versions;"); // Quick and dirty check to see if database is installed TODO: do this better.

                } catch (\Exception $e) {
                    if (strpos($e->getMessage(), 'no such table') !== false) {
                        // Database not installed, try and install it to dbname
                        $dbh      = new \PDO($connection_string);
                        $filename = dirname(dirname(dirname(__FILE__))) . '/schemas/sqlite3/sqlite3.sql';
                        if (file_exists($filename)) {
                            $dbh->exec(@file_get_contents($filename));
                        } else {
                            http_response_code(500);
                            $messages = '<p>We couldn\'t find the schema doc.</p>';
                            die($messages);
                        }

                    } else {

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
                        if ($version->label === 'schema') {
                            $basedate          = $newdate = (int)$version->value;
                            $upgrade_sql_files = array();
                            $schema_dir        = dirname(dirname(dirname(__FILE__))) . '/schemas/sqllite3/';
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
                    $statement = $client->prepare("select * from `versions`");
                    if ($statement->execute()) {
                        return $statement->fetchAll(\PDO::FETCH_OBJ);
                    }
                } catch (\Exception $e) {
                    //\Idno\Core\Idno::site()->logging()->error($e->getMessage());
                    error_log($e->getMessage());
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
                    \Idno\Core\Idno::site()->logging()->error($e->getMessage());

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

                    $statement = $client->prepare("insert or replace into {$collection}
                                                    (`uuid`, `_id`, `entity_subtype`,`owner`, `created`, `contents`)
                                                    values
                                                    (:uuid, :id, :subtype, :owner, :created, :contents)");
                    if ($statement->execute(array(':uuid' => $array['uuid'], ':id' => $array['_id'], ':owner' => $array['owner'], ':subtype' => $array['entity_subtype'], ':contents' => $contents, ':created' => $array['created']))) {

                        // Update FTS Lookup
                        $statement = $client->prepare("insert or replace into {$collection}_search
                            (`uuid`, `search`)
                            values
                            (:uuid, :search)");
                        $statement->execute(array(':uuid' => $array['uuid'], ':search' => $search));

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
                                        \Idno\Core\Idno::site()->logging()->error($e->getMessage());
                                    }
                                }
                                if (empty($value)) {
                                    $value = 0;
                                }
                                if ($statement = $client->prepare("insert into metadata (`collection`, `entity`, `_id`, `name`, `value`) values (:collection, :uuid, :id, :name, :value)")) {
                                    $statement->execute(array('collection' => $collection, ':uuid' => $array['uuid'], ':id' => $array['_id'], ':name' => $key, ':value' => $value));
                                }
                            }
                        }

                        return $array['_id'];
                    }
                } catch (\Exception $e) {
                    \Idno\Core\Idno::site()->logging()->error($e->getMessage());
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
                    \Idno\Core\Idno::site()->logging()->error($e->getMessage());
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
                    $searchjoins      = false;
                    $where            = $this->build_where_from_array($parameters, $variables, $metadata_joins, $non_md_variables, 'and', $collection);
                    for ($i = 1; $i <= $metadata_joins; $i++) {
                        $query .= " left join metadata md{$i} on md{$i}.entity = {$collection}.uuid ";
                    }
                    if (isset($parameters['$search'])) {
                        $query .= " left join {$collection}_search srch on srch.uuid = {$collection}.uuid ";
                    }
                    if (!empty($where)) {
                        $query .= ' where ' . $where . ' ';
                    }
                    $query .= " order by {$collection}.`created` desc limit {$offset},{$limit}";

                    $client = $this->client;
                    /* @var \PDO $client */

                    $statement = $client->prepare($query);

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
                    \Idno\Core\Idno::site()->logging()->error($e->getMessage());

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
                                $subwhere[] = "(`{$collection}`.`{$key}` = :nonmdvalue{$non_md_variables})";
                                if ($key === 'created') {
                                    if (!is_int($value)) {
                                        $value = strtotime($value);
                                    }
                                }
                                $variables[":nonmdvalue{$non_md_variables}"] = $value;
                                $non_md_variables++;
                            } else {
                                $metadata_joins++;
                                $subwhere[]                           = "(md{$metadata_joins}.`name` = :name{$metadata_joins} and md{$metadata_joins}.`value` = :value{$metadata_joins} and md{$metadata_joins}.`collection` = '{$collection}')";
                                $variables[":name{$metadata_joins}"]  = $key;
                                $variables[":value{$metadata_joins}"] = $value;
                            }
                        } else {
                            if (!empty($value['$not'])) {
                                if (!empty($value['$not']['$in'])) {
                                    if (in_array($key, array('uuid', '_id', 'entity_subtype', 'owner'))) {
                                        $notstring = "`{$collection}`.`$key` not in(";
                                        $i         = 0;
                                        foreach ($value['$not']['$in'] as $val) {
                                            if ($i > 0) $notstring .= ', ';
                                            $notstring .= ":nonmdvalue{$non_md_variables}";
                                            $variables[":nonmdvalue{$non_md_variables}"] = $val;
                                            $non_md_variables++;
                                            $i++;
                                        }
                                        $notstring .= ")";
                                    } else {
                                        $metadata_joins++;
                                        $notstring                           = "(md{$metadata_joins}.`name` = :name{$metadata_joins} and md{$metadata_joins}.`collection` = '{$collection}' and md{$metadata_joins}.`value` not in (";
                                        $variables[":name{$metadata_joins}"] = $key;
                                        $i                                   = 0;
                                        foreach ($value['$not']['$in'] as $val) {
                                            if ($i > 0) $notstring .= ', ';
                                            $notstring .= ":nonmdvalue{$non_md_variables}";
                                            $variables[":nonmdvalue{$non_md_variables}"] = $val;
                                            $non_md_variables++;
                                            $i++;
                                        }
                                        $notstring .= "))";
                                    }
                                }
                                // simple $not
                                else {
                                    if (in_array($key, array('uuid', '_id', 'entity_subtype', 'owner'))) {
                                        $notstring                                   = "`{$collection}`.`$key` != :nonmdvalue{$non_md_variables}";
                                        $variables[":nonmdvalue{$non_md_variables}"] = $value['$not'];
                                        $non_md_variables++;
                                    } else {
                                        $metadata_joins++;
                                        $notstring = "(md{$metadata_joins}.`name`    = :name{$metadata_joins} and md{$metadata_joins}.`collection` = '{$collection}' and md{$metadata_joins}.`value` != :nonmdvalue{$non_md_variables})";
                                        $variables[":name{$metadata_joins}"]         = $key;
                                        $variables[":nonmdvalue{$non_md_variables}"] = $value['$not'];
                                        $non_md_variables++;
                                    }
                                }
                                $subwhere[] = $notstring;
                            }
                            if (!empty($value['$in'])) {
                                if (in_array($key, array('uuid', '_id', 'entity_subtype', 'owner'))) {
                                    $instring = "`{$collection}`.`$key` in (";
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
                                    $instring                            = "(md{$metadata_joins}.`name` = :name{$metadata_joins} and md{$metadata_joins}.`collection` = '{$collection}' and md{$metadata_joins}.`value` in (";
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
                            if ($key === '$or') {
                                $subwhere[] = "(" . $this->build_where_from_array($value, $variables, $metadata_joins, $non_md_variables, 'or', $collection) . ")";
                            }
                            if ($key === '$search' && !empty($value)) {
                                $val = $value[0]; // The search query is always in $value position [0] for now
//                                if (strlen($val) > 5) {
//                                    $subwhere[]                                  = " srch.search match :nonmdvalue{$non_md_variables} ";
//                                    $variables[":nonmdvalue{$non_md_variables}"] = "$val*";
//                                } else {
                                $subwhere[]                                  = " srch.search like :nonmdvalue{$non_md_variables}";
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
                    if (isset($parameters['$search'])) {
                        $query .= " left join {$collection}_search srch on srch.uuid = {$collection}.uuid ";
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
                    \Idno\Core\Idno::site()->logging()->error($e->getMessage());

                    return false;
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

                    \Idno\Core\Idno::site()->logging()->error($e->getMessage());

                    return false;

                }

                return false;
            }

            public function exportRecords($collection = 'entities')
            {
                return false; // TODO
            }
        }

    }
