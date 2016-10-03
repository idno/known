<?php


namespace Tests\Data {

    /**
     * Test the acls
     */
    class AccessGroupTest extends \Tests\KnownTestCase {
        
        static $acl;
        static $testUserB;
        
        protected function newObject($owner, $access = 'PUBLIC') {
            $obj = new \Idno\Entities\GenericDataItem();
            $obj->setDatatype('UnitTestObjectAccessGroup');
            $obj->setTitle("Unit Test Search Object");
            $obj->variable1 = 'test';
            $obj->variable2 = 'test again';
            $obj->setOwner($owner);
            $obj->setAccess($access);
            $id = $obj->save();
            
            return $obj;
        }
        
        public static function setupBeforeClass()
        {
            // Create acl
            static::$acl = new \Idno\Entities\AccessGroup();
            
            
            // Create user B
            $user = new \Idno\Entities\User();
            $user->handle = 'testuserb';
            $user->email = 'hello@withknown.com';
            $user->setPassword(md5(rand())); // Set password to something random to mitigate security holes if cleanup fails
            $user->setTitle('Test User B');

            $user->save();

            static::$testUserB = $user;
            
        }
        
        public function testPrivateObject() {
            
            $user = $this->user();
            $old = $this->swapUser($user);
            
            // Add to acl
            static::$acl->addMember($user->getUUID());
            static::$acl->save();
            
            $obj = $this->newObject($user, static::$acl->getUUID());
            
            // Swap user
            $a = $this->swapUser(static::$testUserB);
            
            // Check that B can't access object
            $tmp = \Idno\Entities\GenericDataItem::getByUUID($obj->getUUID());
            $this->assertTrue(empty($tmp));
            
            // Check that A can
            $b = $this->swapUser($a);
            $tmp = \Idno\Entities\GenericDataItem::getByUUID($obj->getUUID());
            $this->assertFalse(empty($tmp));
            
            // Check Admin can always read
            $admin = $this->admin();
            $this->swapUser($admin);
            
            $tmp = \Idno\Entities\GenericDataItem::getByUUID($obj->getUUID());
            $this->assertFalse(empty($tmp));
            
            // Restore old user if there was one
            $this->swapUser($old);
        }
        
        public static function tearDownAfterClass()
        {
            static::$acl->delete(); 
        }
    }
    
}
