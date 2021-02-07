<?php

namespace Tests\Core {

    /**
     * Test session handling code.
     */
    class SessionTest extends \Tests\KnownTestCase
    {

        /**
         * Test user login/logout.
         * Primarily to test the session code in the DataConciege.
         */
        public function testLogInAndOut()
        {

            $user = $this->user();

            // Have we created a user?
            $this->assertTrue(is_object($user));

            // Has logon reported ok?
            $this->assertTrue(is_object(\Idno\Core\Idno::site()->session()->logUserOn($user)), 'The user should have been logged on.');

            // Verify logon
            $this->assertEquals($_SESSION['user_uuid'], $user->getUUID(), 'The user we logged in should be the currently logged-in user.');
            $this->assertTrue(is_object(\Idno\Core\Idno::site()->session()->currentUser()), 'After logging on, should have a complete user object.');

            //Verify logoff
            \Idno\Core\Idno::site()->session()->logUserOff();

            $this->assertArrayNotHasKey('user_uuid', $_SESSION, 'Once we log off, the user UUID should be missing from the session.');
            $this->assertFalse(is_object(\Idno\Core\Idno::site()->session()->currentUser()), 'After logging off, site()->session()->currentuser() should not return an object.');
        }

        public static function tearDownAfterClass():void
        {
            \Idno\Core\Idno::site()->session()->logUserOff();
        }
    }

}
