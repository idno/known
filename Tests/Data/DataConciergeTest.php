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

        public static $fts_objects;

        public static function setUpBeforeClass()
        {
            $obj = new \Idno\Entities\GenericDataItem();
            $obj->setDatatype('UnitTestObject');
            $obj->setTitle("Unit Test Search Object");
            $obj->variable1 = 'test';
            $obj->variable2 = 'test again';
            $id = $obj->save();

            // Save for later retrieval
            self::$id = $id;
            self::$uuid = $obj->getUUID();
            self::$url = $obj->getUrl();
            self::$object = $obj;
        }

        /**
         * Versions test (if applicable)
         */
        public function testVersions() {
            if (is_callable([\Idno\Core\Idno::site()->db(), 'getVersions'])) {
                $versions = \Idno\Core\Idno::site()->db()->getVersions();

                $this->assertTrue(is_array($versions));
            }
        }

        /**
         * Create an object.
         */
        public function testCreateObject() {
            // Verify
            $this->assertFalse(empty(self::$id));
            $this->assertTrue(is_string(self::$uuid));
            $this->assertTrue(is_string(self::$url));

            $this->validateObject(self::$object);
        }

        /**
         * Attempt to retrieve record by UUID.
         */
        public function testGetRecordByUUID() {
            $this->validateObject(
                    \Idno\Core\Idno::site()->db()->rowToEntity(
                            \Idno\Core\Idno::site()->db()->getRecordByUUID(self::$uuid)
                    )
            );
        }

        /**
         * Attempt to retrieve record by ID.
         */
        public function testGetRecord() {
            $this->validateObject(
                    \Idno\Core\Idno::site()->db()->rowToEntity(
                            \Idno\Core\Idno::site()->db()->getRecord(self::$id)
                    )
            );
        }

        /**
         * Attempt to get any object
         */
        public function testGetAnyRecord() {
            $arr = \Idno\Core\Idno::site()->db()->getAnyRecord();

            $this->assertFalse(empty($arr));
            $this->assertTrue(is_array($arr));
            $obj = \Idno\Core\Idno::site()->db()->rowToEntity($arr);
            $this->assertTrue(is_object($obj));
        }

        /**
         * Test getByID
         */
        public function testGetById() {
            $obj = \Idno\Entities\GenericDataItem::getByID(self::$id);

            $this->validateObject($obj);
        }

        /**
         * Test getByID
         */
        public function testGetByUUID() {
            $obj = \Idno\Entities\GenericDataItem::getByUUID(self::$uuid);

            $this->validateObject($obj);
        }

        public function testGetByMetadata() {

            $null = \Idno\Entities\GenericDataItem::get(['variable1' => 'not']);
            $this->assertTrue(empty($null));

            $objs = \Idno\Entities\GenericDataItem::get(['variable1' => 'test']);
            $this->assertTrue(is_array($objs));
            $this->validateObject($objs[0]);
        }

        public function testGetByMetadataMulti() {

            $null = \Idno\Entities\GenericDataItem::get(['variable1' => 'test', 'variable2' => 'not']);
            $this->assertTrue(empty($null));

            $objs = \Idno\Entities\GenericDataItem::get(['variable1' => 'test', 'variable2' => 'test again']);
            $this->assertTrue(is_array($objs));
            $this->validateObject($objs[0]);
        }

        public function testSearchShort() {
            $search = array();

            $search = \Idno\Core\Idno::site()->db()->createSearchArray("sear");

            $count = \Idno\Entities\GenericDataItem::countFromX('Idno\Entities\GenericDataItem', $search);
            $this->assertTrue(is_int($count));
            $this->assertTrue($count > 0);

            $feed  = \Idno\Entities\GenericDataItem::getFromX('Idno\Entities\GenericDataItem', $search);
            $this->assertTrue(is_array($feed));
            $this->assertTrue(($feed[0] instanceof \Idno\Entities\GenericDataItem));
        }

        public function testSearchLong() {

            /** Create couple of FTS objects, since MySQL FTS tables operate in natural language mode */
            $obj = new \Idno\Entities\GenericDataItem();
            $obj->setDatatype('UnitTestObject');
            $obj->setTitle("This is a test obj to get around MySQL natural language mode");
            $obj->variable1 = 'test';
            $obj->variable2 = 'test again';
            $id = $obj->save();

            $obj2 = new \Idno\Entities\GenericDataItem();
            $obj2->setDatatype('UnitTestObject');
            $obj2->setTitle("This is some other text because mysql is a pain.");
            $obj2->variable1 = 'test';
            $obj2->variable2 = 'test again';
            $id = $obj2->save();

            self::$fts_objects = [$obj, $obj2];


            $search = array();

            $search = \Idno\Core\Idno::site()->db()->createSearchArray("language");

            $count = \Idno\Entities\GenericDataItem::countFromX('Idno\Entities\GenericDataItem', $search);
            $this->assertTrue(is_int($count));
            $this->assertTrue($count > 0);

            $feed  = \Idno\Entities\GenericDataItem::getFromX('Idno\Entities\GenericDataItem', $search);
            $this->assertTrue(is_array($feed));
            $this->assertTrue(($feed[0] instanceof \Idno\Entities\GenericDataItem));

            // Clean up
            if (static::$fts_objects) {
                foreach (static::$fts_objects as $obj) {
                    $obj->delete();
                }
            }
        }

        public function testCountObjects() {
            $cnt = \Idno\Entities\GenericDataItem::count(['variable1' => 'test']);

            $this->assertTrue(is_int($cnt));
            $this->assertTrue($cnt > 0);
        }

        /**
         * Helper function to validate object.
         */
        protected function validateObject($obj) {

            $this->assertTrue($obj instanceof \Idno\Entities\GenericDataItem);
            $this->assertEquals("".self::$object->getID(), "".$obj->getID());
            $this->assertEquals("".self::$id, "".$obj->getID());
            $this->assertEquals(self::$uuid, $obj->getUUID());
            $this->assertEquals(self::$url, $obj->getUrl());
        }

        public static function tearDownAfterClass() {
            if (static::$object) static::$object->delete();
            if (static::$fts_objects) {
                foreach (static::$fts_objects as $obj) {
                    $obj->delete();
                }
            }
        }
    }
}

//  get by metadata, search