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
        public function &testUser() {
            
            if (static::$testUser) 
                return static::$testUser;
            
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
        public function &testAdmin() {
            
            if (static::$testAdmin) 
                return static::$testAdmin;
            
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
    }

}
