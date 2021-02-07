<?php

namespace Tests\Data {

    /**
     * Test the currently configured DataConcierge.
     */
    class DataConciergeTest extends \Tests\KnownTestCase
    {

        public static $object;
        public static $uuid;
        public static $id;
        public static $url;

        public static $fts_objects;

        public static function setUpBeforeClass():void
        {
            if (get_called_class() === 'Tests\Data\DataConciergeTest') {
                $obj = new \Idno\Entities\GenericDataItem();
                $obj->setDatatype('UnitTestObject');
                $obj->setTitle("Unit Test Search Object");
                $obj->variable1 = 'test';
                $obj->variable2 = 'test again';
                $obj->rangeVariable = 'b';

                //echo "\n\n\nabout to save";
                $id = $obj->save(); //die($id);

                // Save for later retrieval
                self::$id = $id;
                self::$uuid = $obj->getUUID();
                self::$url = $obj->getUrl();
                self::$object = $obj;
            }
        }

        /**
         * Versions test (if applicable)
         */
        public function testVersions()
        {
            if (is_callable([\Idno\Core\Idno::site()->db(), 'getVersions'])) {
                $versions = \Idno\Core\Idno::site()->db()->getVersions();

                $this->assertTrue(is_array($versions), 'When versions are callable, getVersions should return all version data.');
            }
        }

        /**
         * Create an object.
         */
        public function testCreateObject()
        {
            // Verify
            $this->assertFalse(empty(self::$id));
            $this->assertTrue(is_string(self::$uuid));
            $this->assertTrue(is_string(self::$url));

            $this->validateObject(self::$object);
        }

        /**
         * Attempt to retrieve record by UUID.
         */
        public function testGetRecordByUUID()
        {
            $this->validateObject(
                \Idno\Core\Idno::site()->db()->rowToEntity(
                    \Idno\Core\Idno::site()->db()->getRecordByUUID(self::$uuid)
                )
            );
        }

        /**
         * Attempt to retrieve record by ID.
         */
        public function testGetRecord()
        {
            $this->validateObject(
                \Idno\Core\Idno::site()->db()->rowToEntity(
                    \Idno\Core\Idno::site()->db()->getRecord(self::$id)
                )
            );
        }

        /**
         * Attempt to get any object
         */
        public function testGetAnyRecord()
        {
            $arr = \Idno\Core\Idno::site()->db()->getAnyRecord();

            $this->assertFalse(empty($arr));
            $this->assertTrue(is_array($arr));
            $obj = \Idno\Core\Idno::site()->db()->rowToEntity($arr);
            $this->assertTrue(is_object($obj));
        }

        /**
         * Test getByID
         */
        public function testGetById()
        {
            $obj = \Idno\Entities\GenericDataItem::getByID(self::$id);

            $this->validateObject($obj);
        }

        /**
         * Test getByID
         */
        public function testGetByUUID()
        {
            $obj = \Idno\Entities\GenericDataItem::getByUUID(self::$uuid);

            $this->validateObject($obj);
        }

        public function testGetByURL()
        {
            $obj = \Idno\Entities\GenericDataItem::getByURL(self::$url);

            $this->validateObject($obj);
        }

        public function testGetByMetadata()
        {

            $null = \Idno\Entities\GenericDataItem::get(['variable1' => 'not']);
            $this->assertEmpty($null,);

            $objs = \Idno\Entities\GenericDataItem::get(['variable1' => 'test']);
            $this->assertTrue(is_array($objs), 'Should have returned an array of objects.');
            $this->validateObject($objs[0]);
        }

        public function testGetByMetadataMulti()
        {

            $null = \Idno\Entities\GenericDataItem::get(['variable1' => 'test', 'variable2' => 'not']);
            $this->assertEmpty($null, 'We should not have retrieved any entities.');

            $objs = \Idno\Entities\GenericDataItem::get(['variable1' => 'test', 'variable2' => 'test again']);
            $this->assertTrue(is_array($objs), 'We should have retrieved entities.');
            $this->validateObject($objs[0]);
        }

        /* testing range queries – note: metadata variables are strored as TEXT in SQL backends */
        public function testGetByRange()
        {
            $search = array();
            $search['rangeVariable'] = array();
            $search['rangeVariable']['$lt'] = 'c';
            $search['rangeVariable']['$gt'] = 'a';

            $count = \Idno\Entities\GenericDataItem::countFromX('Idno\Entities\GenericDataItem', $search);
            $this->assertTrue(is_int($count), 'A count of entities should be an integer.');
            $this->assertEquals($count, 1,  '1 entity should match our query.');
        }

        public function testGetByRangeNoResults()
        {
            $search = array();
            $search['rangeVariable'] = array();
            $search['rangeVariable']['$lt'] = 'e';
            $search['rangeVariable']['$gt'] = 'c';

            $count = \Idno\Entities\GenericDataItem::countFromX('Idno\Entities\GenericDataItem', $search);
            $this->assertTrue(is_int($count), 'A count of entities should be an integer.');
            $this->assertEquals($count, 0,  'No entities should match our query.');
        }

        public function testSearchShort()
        {
            $search = array();

            $search = \Idno\Core\Idno::site()->db()->createSearchArray("sear");

            $count = \Idno\Entities\GenericDataItem::countFromX('Idno\Entities\GenericDataItem', $search);
            $this->assertTrue(is_int($count));
            $this->assertTrue($count > 0);

            $feed = \Idno\Entities\GenericDataItem::getFromX('Idno\Entities\GenericDataItem', $search);
            $this->assertTrue(is_array($feed), 'A feed should be an array.');
            $this->assertTrue(($feed[0] instanceof \Idno\Entities\GenericDataItem), 'Items in the feed should be of type GenericDataItem.');
        }

        public function testSearchLong()
        {

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
            $this->assertTrue(is_int($count), 'A count of entities should be an integer.');
            $this->assertTrue($count > 0, 'We should have matched a non-zero number of entities.');

            $feed = \Idno\Entities\GenericDataItem::getFromX('Idno\Entities\GenericDataItem', $search);
            $this->assertTrue(is_array($feed), 'A feed should be an array.');
            $this->assertTrue(($feed[0] instanceof \Idno\Entities\GenericDataItem), 'The first item in the feed should be a GenericDataItem.');

            // Clean up
            if (static::$fts_objects) {
                foreach (static::$fts_objects as $obj) {
                    $obj->delete();
                }
            }
        }

        public function testCountObjects()
        {
            $cnt = \Idno\Entities\GenericDataItem::count(['variable1' => 'test']);

            $this->assertTrue(is_int($cnt), 'A count of entities should be an array.');
            $this->assertTrue($cnt > 0, 'We should have matched a non-zero number of entities.');
        }

        /**
         * Helper function to validate object.
         */
        protected function validateObject($obj)
        {

            var_export($obj);
            var_export(self::$uuid);
            $this->assertTrue($obj instanceof \Idno\Entities\GenericDataItem);

            $this->assertEquals("" . self::$object->getID(), "" . $obj->getID(), 'The object should have a matching ID.');
            $this->assertEquals("" . self::$id, "" . $obj->getID(), 'The object should have a matching ID.');
            $this->assertEquals(self::$uuid, $obj->getUUID(), 'getUUD() should return the object UUID.');
            $this->assertEquals(self::$url, $obj->getUrl(), 'getURL() should return the object URL.');
        }

        public static function tearDownAfterClass():void
        {
            if (self::$object) self::$object->delete();
            if (self::$fts_objects) {
                foreach (self::$fts_objects as $obj) {
                    $obj->delete();
                }
            }
        }
    }
}

//  get by metadata, search
