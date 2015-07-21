<?php

namespace Tests\Data {
    
    /**
     * Test the currently configured DataConcierge.
     */
    class DataConciergeTest extends Tests\KnownTestCase {
        
        public static $object;
        
        
        
        public static function tearDownAfterClass() {
            if (static::$object) static::$object->delete();
        }
    }
}

