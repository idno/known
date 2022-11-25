<?php

namespace IdnoPlugins\OAuth2Client\Tests;

use Firebase\JWT\JWT;
use IdnoPlugins\OAuth2\OIDCToken;

class BearerAuthenticationTest extends \Tests\KnownTestCase {
    
    public function oidcTokenProvider() {
        
        $user = new \Idno\Entities\User();
        $user->handle = 'Test Auth User';
        $user->email = 'hello@withknown.com';
        $user->setPassword(md5(rand())); // Set password to something random to mitigate security holes if cleanup fails
        $user->setTitle('Test Auth');

        $user->save();
            
        $application = \IdnoPlugins\OAuth2\Application::newApplication('test application');
        $application->save(true);
        
        $token = new \IdnoPlugins\OAuth2\Token();
        $token->setOwner($user);
        $token->scope = 'openid email profile';
        $token->key = $application->key;
        
        $oidc = \IdnoPlugins\OAuth2\OIDCToken::generate($token);
        
        $user->delete();
        
        
        $client = new \IdnoPlugins\OAuth2Client\Entities\OAuth2Client();
        $client->federation = true;
        $client->client_id = $application->key;
        $client->publickey = $application->getPublicKey();
        $client->save(true);
        
        return [
            'Test OIDC' => [
                $oidc,
                $client,
                $application
            ]
        ];
        
        
    } 
    
    /**
     * Test to see if we have a token that can be signed and validated
     * @param type $oidc
     * @param type $application
     * @dataProvider oidcTokenProvider
     */
    public function testAuthenticate($oidc, $client, $application) {
        
        $_SERVER['HTTP_AUTHORIZATION'] = "Bearer " . JWT::encode($oidc, $application->getPrivateKey(), 'RS256'); // Fudge a bearer token
        
        $newuser = \IdnoPlugins\OAuth2Client\Main::authenticate();
        
        $this->assertNotEmpty($newuser);
        $this->assertTrue($newuser instanceof \Idno\Entities\RemoteUser);
        
        $this->assertEquals($newuser->getName(), $oidc['name']);
        $this->assertEquals($newuser->oauth2_userid, $oidc['aud'] . '_' . $oidc['sub']);
        $this->assertEquals($newuser->email, $oidc['email']);
        
        $newuser2 = \IdnoPlugins\OAuth2Client\Main::authenticate();
        $this->assertEquals("".$newuser->getID(), "".$newuser2->getID());
        
        $application->delete();
        $newuser->delete();
        $newuser2->delete();
        $client->delete();
    }
    
    
    
}