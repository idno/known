<?php

namespace Tests\Data {
    
    /**
     * Test running configuration and save / load.
     */
    class ConfigTest extends \Tests\KnownTestCase  {
        
        /**
         * Like the Highlander, there can be only one.
         */
        public function testMultipleConfig() {
            $configs = \Idno\Core\Idno::site()->db()->getRecords([], [], 10, 0, 'config');
            $this->assertCount(1, $configs);
        }
        
        /**
         * Ensure that config collection has been correctly configured.
         */
        public function testSave() {
            $this->assertTrue(\Idno\Core\Idno::site()->config()->save()!==false);
        }
        
        
        
    }
    
}
