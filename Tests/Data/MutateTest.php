<?php

namespace Tests\Data;

class MutateTest extends \Tests\KnownTestCase 
{
    
    public function testMutation() {
    
        $remoteuser = new \Idno\Entities\RemoteUser();
        $remoteuser->handle = 'Test Mutation User';
        $remoteuser->email = 'hello@withknown.com';
        $remoteuser->setPassword(md5(rand())); // Set password to something random to mitigate security holes if cleanup fails
        $remoteuser->setTitle('Test Mutation');

        $id = $remoteuser->save();
        
        
        $this->assertNotEmpty($remoteuser->mutate(\Idno\Entities\User::class));
        
        
        $this->assertNotEmpty(\Idno\Entities\RemoteUser::getByID($id));
        $user = \Idno\Entities\User::getByID($id);
        
        $this->assertNotEmpty($user);
                
        foreach ([
            'handle',
            'title',
            'email'
        ] as $key) {
            $this->assertEquals($user->$key, $remoteuser->$key);
        }
        
        $remoteuser->delete();
    }
    
    
}
