<?php

    /**
     * Postgres back-end for Known data.
     *
     * @package idno
     * @subpackage data
     */

namespace Idno\Data {

    class Postgres extends AbstractSQL
    {

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
                    if ($version->label === 'schema') {
                        $basedate          = $newdate = (int)$version->value;
                        $upgrade_sql_files = array();
                        $schema_dir        = dirname(dirname(dirname(__FILE__))) . '/warmup/schemas/postgres/';
                        $client            = $this->client;

                        foreach ([
                            // List upgrades, add yours to the end
                            2016102601,
                            2016110301,
                            2017032001,
                            2019060501,
                            2019121401,
                        ] as $date) {
                            if ($basedate < $date) {
                                if ($sql = @file_get_contents($schema_dir . $date . '.sql')) {

                                    error_log("Applying schema updates from {$schema_dir}{$date}.sql");

                                    $statements = explode(";\n", $sql); // Explode statements; only mysql can support multiple statements per line, and then only badly.
                                    foreach ($statements as $sql) {
                                        $sql = trim($sql);
                                        if (!empty($sql)) {
                                            try {
                                                $statement = $client->prepare($sql);
                                                $statement->execute();
                                            } catch (\Exception $e) {
                                                error_log($e->getMessage());
                                            }
                                        }
                                    }
                                }
                                $newdate = $date;
                            }
                        }

                    }
                }
            }
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
                $array['_id'] = md5(mt_rand() . microtime(true));
            }
            if (empty($array['uuid'])) {
                $array['uuid'] = \Idno\Core\Idno::site()->config()->getURL() . 'view/' . $array['_id'];
            }
            if (empty($array['owner'])) {
                $array['owner'] = '';
            }
            try {
                $contents = json_encode($array);

                if (json_last_error() != JSON_ERROR_NONE)
                    throw new \Exception(json_last_error_msg());
            } catch (\Exception $e) {
                $contents = json_encode([]);
                \Idno\Core\Idno::site()->logging()->error('Exception saving record', ['error' => $e]);

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
            if (empty($array['publish_status'])) {
                $array['publish_status'] = 'published';
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
                               contents=:contents, publish_status=:status where _id=:id";
                $insert = "insert into {$collection}
                               (uuid, _id, owner, entity_subtype, contents, publish_status)
                               select :uuid, :id, :owner, :subtype, :contents, :status";

                $statement = $client->prepare("with upsert as (${upsert} returning *)
                                                   ${insert} where not exists (select * from upsert)");

                if ($statement->execute(array(':uuid' => $array['uuid'], ':id' => $array['_id'], ':owner' => $array['owner'], ':subtype' => $array['entity_subtype'], ':contents' => $contents, ':status' => $array['publish_status']))) {
                    
                    // Update FTS
                    $upsert = "update {$collection}_search
                               set search=:search 
                               where _id=:id";
                    $insert = "insert into {$collection}_search
                                   (_id, search)
                                   select :id, :search";

                    $statement = $client->prepare("with upsert as (${upsert} returning *)
                                                   ${insert} where not exists (select * from upsert)");
                                                  
                    $statement->execute(array(':id' => $array['_id'], ':search' => $search));

                    
                                                   
                                                   
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

                                    if (json_last_error() != JSON_ERROR_NONE)
                                        throw new \Exception(json_last_error_msg());
                                } catch (\Exception $e) {
                                    $value = json_encode([]);
                                    \Idno\Core\Idno::site()->logging()->error($e->getMessage());
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
                //\Idno\Core\Idno::site()->logging()->error($e->getMessage());
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

                $statement = $this->client->prepare("select {$collection}.* from " . $collection . " order by {$collection}.created desc limit 1");
                if ($statement->execute()) {
                    if ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                        return json_decode($row['contents'], true);
                    }
                }
            } catch (\Exception $e) {
                if (\Idno\Core\Idno::site()->session() == null)
                    throw $e;
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
                $where            = $this->build_where_from_array($parameters, $variables, $metadata_joins, $non_md_variables, 'and', $collection);
                for ($i = 1; $i <= $metadata_joins; $i++) {
                    $query .= " left join metadata md{$i} on md{$i}.entity = {$collection}.uuid ";
                }
                if (isset($parameters['$search'])) {
                    $query .= " left join {$collection}_search srch on srch._id = {$collection}._id ";
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
                \Idno\Core\Idno::site()->logging()->error('Exception while fetching records', ['error' => $e]);

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
                        if (in_array($key, $this->getSchemaFields())) {
                            $subwhere[] = "({$collection}.{$key} = :nonmdvalue{$non_md_variables})";
                            if ($key === 'created') {
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
                        if (!empty($value['$not'])) {
                            if (!empty($value['$not']['$in'])) {
                                if (in_array($key, $this->getSchemaFields())) {
                                    $notstring = "{$collection}.$key not in (";
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
                                    $notstring                           = "(md{$metadata_joins}.name = :name{$metadata_joins} and md{$metadata_joins}.collection = '{$collection}' and md{$metadata_joins}.value not in (";
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
                                if (in_array($key, $this->getSchemaFields())) {
                                    $notstring                                   = "{$collection}.{$key} != :nonmdvalue{$non_md_variables}";
                                    $variables[":nonmdvalue{$non_md_variables}"] = $value['$not'];
                                    $non_md_variables++;
                                } else {
                                    $metadata_joins++;
                                    $notstring = "(md{$metadata_joins}.`name`    = :name{$metadata_joins} and md{$metadata_joins}.collection = '{$collection}' and md{$metadata_joins}.value != :nonmdvalue{$non_md_variables})";
                                    $variables[":name{$metadata_joins}"]         = $key;
                                    $variables[":nonmdvalue{$non_md_variables}"] = $value['$not'];
                                    $non_md_variables++;
                                }
                            }
                            $subwhere[] = $notstring;
                        }
                        if (!empty($value['$in'])) {
                            if (in_array($key, array('uuid', '_id', 'entity_subtype', 'owner', 'publish_status'))) {
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
                        if (!empty($value['$lt'])) {
                            $val = $value['$lt'];
                            if (in_array($key, $this->getSchemaFields())) {
                                $subwhere[] = "({$collection}.{$key} < :nonmdvalue{$non_md_variables})";
                                if ($key === 'created') {
                                    if (!is_int($val)) {
                                        $val = strtotime($val);
                                    }
                                }
                                $variables[":nonmdvalue{$non_md_variables}"] = $val;
                                $non_md_variables++;
                            } else {
                                $metadata_joins++;
                                $subwhere[]                           = "(md{$metadata_joins}.name = :name{$metadata_joins} and md{$metadata_joins}.value < :value{$metadata_joins} and md{$metadata_joins}.collection = '{$collection}')";
                                $variables[":name{$metadata_joins}"]  = $key;
                                $variables[":value{$metadata_joins}"] = $val;
                            }
                        }
                        if (!empty($value['$gt'])) {
                            $val = $value['$gt'];
                            if (in_array($key, $this->getSchemaFields())) {
                                $subwhere[] = "({$collection}.{$key} > :nonmdvalue{$non_md_variables})";
                                if ($key === 'created') {
                                    if (!is_int($val)) {
                                        $value = strtotime($val);
                                    }
                                }
                                $variables[":nonmdvalue{$non_md_variables}"] = $val;
                                $non_md_variables++;
                            } else {
                                $metadata_joins++;
                                $subwhere[]                           = "(md{$metadata_joins}.name = :name{$metadata_joins} and md{$metadata_joins}.value > :value{$metadata_joins} and md{$metadata_joins}.collection = '{$collection}')";
                                $variables[":name{$metadata_joins}"]  = $key;
                                $variables[":value{$metadata_joins}"] = $val;
                            }
                        }
                        if ($key === '$or') {
                            $subwhere[] = "(" . $this->build_where_from_array($value, $variables, $metadata_joins, $non_md_variables, 'or', $collection) . ")";
                        }
                        if ($key === '$search' && !empty($value)) {
                            $val = $value[0]; // The search query is always in $value position [0] for now
                            //                                if (strlen($val) > 5) {
                            //                                    $subwhere[]                                  = "match (search) against (:nonmdvalue{$non_md_variables})";
                            //                                    $variables[":nonmdvalue{$non_md_variables}"] = $val;
                            //                                } else {
                            $subwhere[]                                  = "srch.search like :nonmdvalue{$non_md_variables}";
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
        function exportRecords($collection = 'entities', $limit = 10, $offset = 0)
        {
            // TODO
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
                    $query .= " left join {$collection}_search srch on srch._id = {$collection}._id ";
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
                \Idno\Core\Idno::site()->logging()->error('Exception while fetching objects', ['error' => $e]);

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

                \Idno\Core\Idno::site()->logging()->error('Exception deleting record', ['error' => $e]);

                return false;

            }

            return false;
        }

        /**
         * Remove all entities from a collection from the database
         * @param string $collection
         * @return bool
         */
        function deleteAllRecords($collection)
        {
            try {
                if (empty($collection)) return false;
                $collection = $this->sanitiseCollection($collection);

                $client = $this->client;
                /* @var \PDO $client */
                $statement = $client->prepare("delete from {$collection}");
                if ($statement->execute()) {
                    
                    if ($statement = $client->prepare("delete from metadata where collection = :collection")) {
                        return $statement->execute([':collection' => $collection]);
                    }
                }
            } catch (\Exception $e) {
                \Idno\Core\Idno::site()->logging()->error($e->getMessage());
                return false;
            }
        }

    }

}
