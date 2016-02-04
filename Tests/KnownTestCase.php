<?php

namespace Tests {

    class KnownTestCase extends \PHPUnit_Framework_TestCase {

        /// Admin user
        public static $testAdmin;

        /// Regular user
        public static $testUser;

        /**
         * Return a test user, creating it if necessary.
         * @return \Idno\Entities\User
         */
        protected function &user() {

            // Have we already got a user?
            if (static::$testUser)
                return static::$testUser;

            // Get a user (shouldn't happen)
            if ($user = \Idno\Entities\User::getByHandle('testuser'))
            {
                static::$testUser = $user;

                return $user;
            }

            // No user there, so create one
            $user = new \Idno\Entities\User();
            $user->handle = 'testuser';
            $user->email = 'hello@withknown.com';
            $user->setPassword(md5(rand())); // Set password to something random to mitigate security holes if cleanup fails
            $user->setTitle('Test User');

            $user->save();

            static::$testUser = $user;

            return $user;
        }

        /**
         * Return an admin test user, creating it if necessary.
         * @return \Idno\Entities\User
         */
        protected function &admin() {

            // Have we already got a user?
            if (static::$testAdmin)
                return static::$testAdmin;

            // Get a user (shouldn't happen)
            if ($user = \Idno\Entities\User::getByHandle('testadmin'))
            {
                static::$testAdmin = $user;

                return $user;
            }

            // No user there, so create one
            $user = new \Idno\Entities\User();
            $user->handle = 'testadmin';
            $user->email = 'hello@withknown.com';
            $user->setPassword(md5(rand())); // Set password to something random to mitigate security holes if cleanup fails
            $user->setTitle('Test Admin User');
            $user->setAdmin(true);

            $user->save();

            static::$testAdmin = $user;

            return $user;
        }

        /**
         * Set settings.
         */
        public static function setupBeforeClass()
        {
            \Idno\Core\Idno::site()->config()->hub = '';
        }

        /**
         * Clean up framework.
         */
        public static function tearDownAfterClass()
        {
            // Delete users, if we've created some but forgot to clean up
            if (static::$testUser) {
                static::$testUser->delete();
                static::$testUser = false;
            }
            if (static::$testAdmin) {
                static::$testAdmin->delete();
                static::$testAdmin = false;
            }
        }
    }

}
