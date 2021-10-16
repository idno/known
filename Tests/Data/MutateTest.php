<?php

namespace Tests\Data;

class MutateTest extends \Tests\KnownTestCase
{

    public function testMutation()
    {

        $remoteuser = new \Idno\Entities\RemoteUser();
        $remoteuser->handle = 'Test Mutation User';
        $remoteuser->email = 'hello@withknown.com';
        $remoteuser->setPassword(md5(openssl_random_pseudo_bytes(16))); // Set password to something random to mitigate security holes if cleanup fails
        $remoteuser->setTitle('Test Mutation');

        $id = $remoteuser->save(true);

        $this->assertNotEmpty($remoteuser->mutate(\Idno\Entities\User::class));

        $this->assertNotEmpty(\Idno\Entities\RemoteUser::getByID($id));
        $user = \Idno\Entities\User::getByID($id);

        $this->assertInstanceOf('\Idno\Entities\User', $user, 'The retrieved user should be a User entity.');
        $this->assertNotEmpty($user, 'The user entity should be populated.');

        foreach ([
            'handle',
            'title',
            'email'
        ] as $key) {
            $this->assertEquals($user->$key, $remoteuser->$key, 'Property ' . $key . ' should be ' . $remoteuser->$key . '.');
        }

        $remoteuser->delete();
    }


}
