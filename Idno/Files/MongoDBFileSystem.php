<?php

    namespace Idno\Files {

        class MongoDBFileSystem extends FileSystem
        {
            
            private $manager;
            private $dbname;
            
            private $gridfs_object;

            public function __construct($manager, $dbname) {
                $this->manager = $manager;
                $this->dbname = $dbname;
                
                $this->gridfs_object = new \MongoDB\GridFS\Bucket($this->manager, $this->dbname);
            }

            public function findOne($_id) {
                
                if (is_array($_id)) {
                    $_id = $_id['_id'];
                }
                
                $result = $this->gridfs_object->find(['_id' => new \MongoDB\BSON\ObjectID($_id)], ['limit' => 1]); 
                if ($array = iterator_to_array($result)) {
                    
                    $data = \Idno\Core\site()->db()->unsanitizeFields($array[0]);
                    
                    $file = new \Idno\Files\MongoDBFile();
                    $file->setBucket($this->gridfs_object);
                    
                    $file->_id               = $_id;
                    
                    foreach ($data as $k => $v) {
                        if ($k != '_id') { // Prevent objects from clobbering the ID
                            $file->metadata[$k] = $v;
                        }
                        
                        if ($v instanceof \MongoDB\BSON\UTCDateTime) { // Handle MongoDB dates
                            $file->metadata[$k] = $v->__toString();
                        }
                    }
                    
                    return $file;                    
                    
                }
                
                return false;
            }

            public function storeFile($file_path, $metadata, $options) {

                $bucket = $this->gridfs_object;
                
                try {
                    
                    if ($source = fopen($file_path, 'rb')) {
                        
                        $id = $bucket->uploadFromStream($file_path, $source, [
                            'metadata' => new \MongoDB\Model\BSONDocument($metadata)
                        ]);
                        
                        fclose($upload);
                    }
                } catch (\Exception $ex) {
                    \Idno\Core\site()->logging()->debug($ex->getMessage());
                }
                
                return false;
            }

        }

    }