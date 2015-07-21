<?php

namespace Tests\Data {
    
    /**
     * Test the currently configured DataConcierge.
     */
    class DataConciergeTest extends \Tests\KnownTestCase {
        
        public static $object;
        public static $uuid;
        public static $id;
        public static $url;
        
        /**
         * Versions test (if applicable)
         */
        public function testVersions() {
            if (is_callable([\Idno\Core\site()->db(), 'getVersions'])) {
                $versions = \Idno\Core\site()->db()->getVersions();
                
                $this->assertTrue(is_array($versions));
            }
        }
        
        /**
         * Create an object.
         */
        public function testCreateObject() {
            
            $obj = new \Idno\Entities\GenericDataItem();
            $obj->setDatatype('UnitTestObject');
            $obj->variable1 = 'test';
            $obj->variable2 = 'test again';
            $id = $obj->save();
            
            // Make sure we've created something
            $this->assertTrue(is_string($id));
            
            // Save for later retrieval
            self::$id = $id;
            self::$uuid = $obj->getUUID();
            self::$url = $obj->getUrl();
            self::$object = $obj;
            
            // Verify
            $this->assertTrue(is_string(self::$id));
            $this->assertTrue(is_string(self::$uuid));
            $this->assertTrue(is_string(self::$url));
        }
        
        /**
         * Attempt to retrieve record by UUID.
         */
        public function testGetByUUID() {
            $this->validateObject(
                    \Idno\Core\site()->db()->rowToEntity(
                            \Idno\Core\site()->db()->getRecordByUUID(self::$uuid)
                    )
            );
        }
        
        /**
         * Attempt to retrieve record by ID.
         */
        public function testGetRecord() {
            $this->validateObject(
                    \Idno\Core\site()->db()->rowToEntity(
                            \Idno\Core\site()->db()->getRecord(self::$id)
                    )
            );
        }
        
        /**
         * Attempt to get any object
         */
        public function testGetAnyRecord() {
            $obj = \Idno\Core\site()->db()->getAnyRecord();
           
            $this->assertTrue(is_object($obj));
        }
        
        /**
         * Helper function to validate object.
         */
        protected function validateObject($obj) {
            
            $this->assertTrue($obj instanceof \Idno\Entities\GenericDataItem);
            $this->assertEquals(self::$object->getID(), $obj->getID());
            $this->assertEquals(self::$id, $obj->getID());
            $this->assertEquals(self::$uuid, $obj->getUUID());
            $this->assertEquals(self::$url, $obj->getUrl());
        }
        
        public static function tearDownAfterClass() {
            if (static::$object) static::$object->delete();
        }
    }
}

// get id, get uuid, get url, get by metadata, search