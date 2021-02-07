<?php


namespace Tests\Data {

    /**
     * Test the acls
     */
    class AccessGroupTest extends \Tests\KnownTestCase
    {

        static $acl;
        static $testUserB;

        protected function newObject($owner, $access = 'PUBLIC')
        {
            $obj = new \Idno\Entities\GenericDataItem();
            $obj->setDatatype('UnitTestObjectAccessGroup');
            $obj->setTitle("Search object for ACL Test");
            $obj->setOwner($owner);
            $obj->setAccess($access);

            return $obj;
        }

        public static function setUpBeforeClass():void
        {
            if (get_called_class() === 'Tests\Data\AccessGroupTest') {
                // Create acl
                self::$acl = new \Idno\Entities\AccessGroup();

                // Create user B
                $user = new \Idno\Entities\User();
                $user->handle = 'testuserb';
                $user->email = 'hello@withknown.com';
                $user->setPassword(md5(rand())); // Set password to something random to mitigate security holes if cleanup fails
                $user->setTitle('Test User B');

                $user->save();

                self::$testUserB = $user;
            }

        }

        public function testPrivateObject()
        {

            $user = $this->user();
            $old = $this->swapUser($user);

            // Add to acl
            self::$acl->addMember($user->getUUID());
            self::$acl->save();

            $obj = $this->newObject($user, self::$acl->getUUID());
            $obj->save();

            // Swap user
            $a = $this->swapUser(self::$testUserB);

            // Check that B can't access object
            $tmp = \Idno\Entities\GenericDataItem::getByUUID($obj->getUUID());
            $this->assertTrue(empty($tmp), 'User A should not be able to access an object with an access group they are not a part of.');

            // Check that A can
            $b = $this->swapUser($a);
            $tmp = \Idno\Entities\GenericDataItem::getByUUID($obj->getUUID());
            var_export($tmp);
            $this->assertTrue(!empty($tmp), 'User B should be able to access an object with an access group they are a part of.');

            // Check Admin can always read
            $admin = $this->admin();
            $this->swapUser($admin);

            $tmp = \Idno\Entities\GenericDataItem::getByUUID($obj->getUUID());
            $this->assertFalse(empty($tmp), 'Admins should always be able to read data.');

            // Test objects in this UUID
            $objs = \Idno\Entities\AccessGroup::getByAccessGroup(self::$acl->getUUID());
            $this->assertTrue(count($objs) == 1, 'Exactly 1 entity with the specified UUID should have been retrieved.');

            $obj->delete();

            // Restore old user if there was one
            $this->swapUser($old);
        }

        // Create an owner only object
        public function testOwnerOnly()
        {
            $user = $this->user();
            $old = $this->swapUser($user);

            // Create user only object
            $obj = $this->newObject($user, $user->getUUID());
            $obj->save();

            // Swap user
            $a = $this->swapUser(self::$testUserB);

            // Check that B can't access object
            $tmp = \Idno\Entities\GenericDataItem::getByUUID($obj->getUUID());
            $this->assertTrue(empty($tmp), 'User B should not be able to access the specified object because they do not have access.');

            // Check that A can
            $b = $this->swapUser($a);
            $tmp = \Idno\Entities\GenericDataItem::getByUUID($obj->getUUID());
            var_export($tmp);
            $this->assertTrue(!empty($tmp), 'User A should be able to access the specified object because they have access.');

            // Check Admin can always read
            $admin = $this->admin();
            $this->swapUser($admin);

            $tmp = \Idno\Entities\GenericDataItem::getByUUID($obj->getUUID());
            $this->assertFalse(empty($tmp), 'Admins should always be able to see objects.');

            $obj->delete();

            // Restore old user if there was one
            $this->swapUser($old);
        }

        /**
         * Ensure duplicate slugs aren't possible with ACLed entities, see https://github.com/idno/Known/issues/1864
         */
        public function testSlugGeneration()
        {

            $user = $this->user();
            $old = $this->swapUser($user);

            // First object
            $obj = new \IdnoPlugins\Status\Status();
            $obj->body = "slugstatusexample";
            $obj->setAccess($user->getUUID());
            $id1 = $obj->save();

            // Second object
            $a = $this->swapUser(self::$testUserB);
            $obj2 = new \IdnoPlugins\Status\Status();
            $obj2->body = "slugstatusexample";
            $obj2->setAccess('PUBLIC');
            $id2 = $obj2->save();

            // Make sure they don't have the same URL
            $this->assertFalse($obj->getUrl() == $obj2->getUrl(), 'Even when we cannot see an object, duplicate slugs should not be possible.');

            $admin = $this->admin();
            $this->swapUser($admin);

            // Delete objects
            $obj2->delete();
            $obj->delete();

            // Restore old user if there was one
            $this->swapUser($old);
        }

        public function testACLBypass()
        {

            $db = \Idno\Core\Idno::site()->db();

            $old = $db->setIgnoreAccess(true);
            $this->assertTrue($db->getIgnoreAccess(), 'When setting ignore access to true, getIgnoreAccess should return true.');

            $old = $db->setIgnoreAccess($old);
            $this->assertFalse($db->getIgnoreAccess(), 'When setting ignore access to false, getIgnoreAccess should return false.');

        }

        public static function tearDownAfterClass():void
        {
            self::$acl->delete();
        }
    }

}
