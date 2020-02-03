<?php

namespace Tests\API {

    use Idno\Core\JWT;
    
    /**
     * Test JWTs
     */
    class JWTTest extends \Tests\KnownTestCase {
        
        public function tokenProvider() {
            return [
                'basic' => [
                    '12346abcd', // user
                    'user', // type
                    time() + 3600 // expiry
                ]
            ];
        }
        
        /**
         * @dataProvider tokenProvider
         * @param type $user
         * @param type $type
         * @param type $exp
         */
        public function testJWT($user, $type, $exp) {
            
            $jwt = new JWT();
            
            $token = "$jwt";
            $this->assertNotEmpty($token);
            
            
            $array = JWT::parse($token);
            
            $this->assertEquals($array['user_id'], $user);
            $this->assertEquals($array['role'], $type); 
            $this->assertEquals($array['exp'], $exp);
            
            
        }
    }

}

