<?php

namespace IdnoPlugins\ActivityPub\Tests {

    use Idno\Entities\User;
    use IdnoPlugins\ActivityPub\ActivityBuilder;
    use IdnoPlugins\ActivityPub\HTTPSignature;
    use PHPUnit\Framework\TestCase;

    /**
     * Tests for ActivityPub plugin functionality.
     *
     * These tests cover ActivityBuilder constants and methods, HTTP signature
     * signing/verification, and signature header parsing. They do not require
     * the full Idno framework or a database connection.
     */
    class ActivityPubTest extends TestCase
    {

        // -----------------------------------------------------------------
        // ActivityBuilder constants
        // -----------------------------------------------------------------

        /**
         * Test that TYPE_MAP contains the expected Idno to ActivityPub mappings.
         */
        public function testTypeMapMapsCorrectly()
        {
            $this->assertEquals('Note', ActivityBuilder::TYPE_MAP['note']);
            $this->assertEquals('Article', ActivityBuilder::TYPE_MAP['article']);
            $this->assertEquals('Note', ActivityBuilder::TYPE_MAP['image']);
            $this->assertEquals('Event', ActivityBuilder::TYPE_MAP['event']);
            $this->assertArrayHasKey('bookmark', ActivityBuilder::TYPE_MAP);
            $this->assertArrayHasKey('rsvp', ActivityBuilder::TYPE_MAP);
        }

        /**
         * Test that NON_CONTENT_SUBTYPES constant contains expected exclusion entries.
         */
        public function testNonContentSubtypesConstant()
        {
            $subtypes = ActivityBuilder::NON_CONTENT_SUBTYPES;

            $this->assertIsArray($subtypes);
            $this->assertNotEmpty($subtypes);

            // Each entry should start with '!' for DB-level exclusion
            foreach ($subtypes as $subtype) {
                $this->assertStringStartsWith('!', $subtype, "Subtype '$subtype' should start with '!' for exclusion");
            }

            // Should exclude User, ActivityPubFollower, and AsynchronousQueuedEvent
            $joined = implode('|', $subtypes);
            $this->assertStringContainsString('User', $joined);
            $this->assertStringContainsString('ActivityPubFollower', $joined);
            $this->assertStringContainsString('AsynchronousQueuedEvent', $joined);
        }

        /**
         * Test that CONTEXT constant is a valid array with ActivityStreams URI.
         */
        public function testContextConstant()
        {
            $this->assertIsArray(ActivityBuilder::CONTEXT);
            $this->assertContains('https://www.w3.org/ns/activitystreams', ActivityBuilder::CONTEXT);
            $this->assertContains('https://w3id.org/security/v1', ActivityBuilder::CONTEXT);
        }

        /**
         * Test that PUBLIC_AUDIENCE constant has the correct value.
         */
        public function testPublicAudienceConstant()
        {
            $this->assertEquals(
                'https://www.w3.org/ns/activitystreams#Public',
                ActivityBuilder::PUBLIC_AUDIENCE
            );
        }

        // -----------------------------------------------------------------
        // ActivityBuilder::wrapActivity (pure array logic, no DB needed)
        // -----------------------------------------------------------------

        /**
         * Test that wrapActivity inherits addressing from object.
         */
        public function testWrapActivityInheritsAddressing()
        {
            $object = [
                'id'   => 'https://example.com/note/1',
                'type' => 'Note',
                'to'   => ['https://www.w3.org/ns/activitystreams#Public'],
                'cc'   => ['https://example.com/actor/test/followers'],
            ];

            $activity = ActivityBuilder::wrapActivity('Create', null, $object, 'https://example.com/note/1#create');

            $this->assertEquals('Create', $activity['type']);
            $this->assertEquals(['https://www.w3.org/ns/activitystreams#Public'], $activity['to']);
            $this->assertEquals(['https://example.com/actor/test/followers'], $activity['cc']);
            $this->assertArrayHasKey('@context', $activity);
            $this->assertArrayHasKey('published', $activity);
        }

        /**
         * Test that wrapActivity does not add to/cc if not present in object.
         */
        public function testWrapActivityNoAddressingWhenObjectLacksIt()
        {
            $object = [
                'id'   => 'https://example.com/note/2',
                'type' => 'Note',
            ];

            $activity = ActivityBuilder::wrapActivity('Create', null, $object, 'https://example.com/note/2#create');

            $this->assertArrayNotHasKey('to', $activity);
            $this->assertArrayNotHasKey('cc', $activity);
        }

        /**
         * Test that wrapActivity uses explicit ID when provided.
         */
        public function testWrapActivityUsesExplicitId()
        {
            $object = ['id' => 'https://example.com/note/1', 'type' => 'Note'];
            $explicitId = 'https://example.com/note/1#create';

            $activity = ActivityBuilder::wrapActivity('Create', null, $object, $explicitId);

            $this->assertEquals($explicitId, $activity['id']);
        }

        // -----------------------------------------------------------------
        // ActivityBuilder::buildAccept
        // -----------------------------------------------------------------

        /**
         * Helper: create a mock User that returns a fixed actor ID.
         */
        private function createMockUser(string $actorId = 'https://example.com/actor/testuser'): User
        {
            $user = $this->createMock(User::class);
            $user->method('getActivityPubActorID')->willReturn($actorId);
            return $user;
        }

        /**
         * Test that buildAccept uses only the ActivityStreams context string.
         * The security/v1 context is not needed and extra contexts can cause
         * issues with strict JSON-LD processors like Fedify.
         */
        public function testBuildAcceptUsesSimpleContext()
        {
            $follow = [
                '@context' => 'https://www.w3.org/ns/activitystreams',
                'id'       => 'https://remote.example/follow/1',
                'type'     => 'Follow',
                'actor'    => 'https://remote.example/users/alice',
                'object'   => 'https://example.com/actor/testuser',
            ];

            $accept = ActivityBuilder::buildAccept($follow, $this->createMockUser());

            $this->assertEquals(
                'https://www.w3.org/ns/activitystreams',
                $accept['@context'],
                'Accept should use only the ActivityStreams context string'
            );
        }

        /**
         * Test that buildAccept produces the correct structure.
         */
        public function testBuildAcceptStructure()
        {
            $follow = [
                '@context' => 'https://www.w3.org/ns/activitystreams',
                'id'       => 'https://remote.example/follow/1',
                'type'     => 'Follow',
                'actor'    => 'https://remote.example/users/alice',
                'object'   => 'https://example.com/actor/testuser',
            ];

            $accept = ActivityBuilder::buildAccept($follow, $this->createMockUser());

            $this->assertEquals('Accept', $accept['type']);
            $this->assertEquals('https://example.com/actor/testuser', $accept['actor']);
            $this->assertStringStartsWith('https://example.com/actor/testuser#accept-', $accept['id']);
            $this->assertArrayHasKey('object', $accept);
        }

        /**
         * Test that the embedded Follow in the Accept contains only the
         * essential standard properties (id, type, actor, object) and no
         * implementation-specific fields from the original Follow.
         */
        public function testBuildAcceptSanitizesEmbeddedFollow()
        {
            $follow = [
                '@context'       => ['https://www.w3.org/ns/activitystreams', 'https://custom.context/v1'],
                'id'             => 'https://remote.example/follow/1',
                'type'           => 'Follow',
                'actor'          => 'https://remote.example/users/alice',
                'object'         => 'https://example.com/actor/testuser',
                'customProperty' => 'should be stripped',
                'published'      => '2026-01-01T00:00:00Z',
            ];

            $accept = ActivityBuilder::buildAccept($follow, $this->createMockUser());
            $embedded = $accept['object'];

            // Only standard Follow properties should be present
            $this->assertEquals('https://remote.example/follow/1', $embedded['id']);
            $this->assertEquals('Follow', $embedded['type']);
            $this->assertEquals('https://remote.example/users/alice', $embedded['actor']);
            $this->assertEquals('https://example.com/actor/testuser', $embedded['object']);

            // @context and implementation-specific fields must NOT be in the embedded Follow
            $this->assertArrayNotHasKey('@context', $embedded, 'Embedded Follow must not have @context');
            $this->assertArrayNotHasKey('customProperty', $embedded, 'Implementation-specific fields must be stripped');
            $this->assertArrayNotHasKey('published', $embedded, 'Non-essential fields must be stripped');

            // Verify the embedded Follow has exactly 4 keys
            $this->assertCount(4, $embedded, 'Embedded Follow should have exactly id, type, actor, object');
        }

        /**
         * Test that the to field in the Accept is an array, not a string.
         * Some implementations (notably Fedify/Ghost) pre-process addressing
         * fields expecting arrays before JSON-LD normalization.
         */
        public function testBuildAcceptToFieldIsArray()
        {
            $follow = [
                'id'     => 'https://remote.example/follow/1',
                'type'   => 'Follow',
                'actor'  => 'https://remote.example/users/alice',
                'object' => 'https://example.com/actor/testuser',
            ];

            $accept = ActivityBuilder::buildAccept($follow, $this->createMockUser());

            $this->assertArrayHasKey('to', $accept);
            $this->assertIsArray($accept['to'], 'The to field must be an array');
            $this->assertEquals(['https://remote.example/users/alice'], $accept['to']);
        }

        /**
         * Test that buildAccept does not include a published field.
         * Mastodon and other implementations that work with Ghost do not
         * include published in Accept activities.
         */
        public function testBuildAcceptDoesNotIncludePublished()
        {
            $follow = [
                'id'     => 'https://remote.example/follow/1',
                'type'   => 'Follow',
                'actor'  => 'https://remote.example/users/alice',
                'object' => 'https://example.com/actor/testuser',
            ];

            $accept = ActivityBuilder::buildAccept($follow, $this->createMockUser());

            $this->assertArrayNotHasKey('published', $accept, 'Accept should not include published');
        }

        /**
         * Test that buildAccept handles Follow with actor/object as objects
         * (not just string URIs) by extracting the id.
         */
        public function testBuildAcceptHandlesObjectActorAsObject()
        {
            $follow = [
                'id'     => 'https://remote.example/follow/2',
                'type'   => 'Follow',
                'actor'  => ['id' => 'https://remote.example/users/bob', 'type' => 'Person'],
                'object' => ['id' => 'https://example.com/actor/testuser', 'type' => 'Person'],
            ];

            $accept = ActivityBuilder::buildAccept($follow, $this->createMockUser());
            $embedded = $accept['object'];

            $this->assertEquals('https://remote.example/users/bob', $embedded['actor']);
            $this->assertEquals('https://example.com/actor/testuser', $embedded['object']);
            $this->assertEquals(['https://remote.example/users/bob'], $accept['to']);
        }

        /**
         * Test that buildAccept generates a deterministic ID based on the
         * Follow activity's ID.
         */
        public function testBuildAcceptDeterministicId()
        {
            $follow = [
                'id'     => 'https://remote.example/follow/stable-id',
                'type'   => 'Follow',
                'actor'  => 'https://remote.example/users/alice',
                'object' => 'https://example.com/actor/testuser',
            ];

            $user = $this->createMockUser();
            $accept1 = ActivityBuilder::buildAccept($follow, $user);
            $accept2 = ActivityBuilder::buildAccept($follow, $user);

            $this->assertEquals($accept1['id'], $accept2['id'], 'Accept ID should be deterministic for the same Follow');
        }

        /**
         * Test that buildAccept omits the to field when the Follow has no actor.
         */
        public function testBuildAcceptOmitsToWhenNoActor()
        {
            $follow = [
                'id'     => 'https://remote.example/follow/1',
                'type'   => 'Follow',
                'object' => 'https://example.com/actor/testuser',
            ];

            $accept = ActivityBuilder::buildAccept($follow, $this->createMockUser());

            $this->assertArrayNotHasKey('to', $accept, 'Accept should not have to field when Follow has no actor');
        }

        // -----------------------------------------------------------------
        // HTTP Signature tests
        // -----------------------------------------------------------------

        /**
         * Test sign and verify round-trip with generated keys.
         */
        public function testSignAndVerifyRoundtrip()
        {
            $config = [
                'digest_alg'       => 'sha512',
                'private_key_bits' => 2048,
                'private_key_type' => OPENSSL_KEYTYPE_RSA,
            ];
            $key = openssl_pkey_new($config);
            $privateKey = '';
            openssl_pkey_export($key, $privateKey);
            $detail = openssl_pkey_get_details($key);
            $publicKey = $detail['key'];

            $keyId = 'https://example.com/actor/test#main-key';
            $url = 'https://remote.example/inbox';
            $body = json_encode(['type' => 'Create', 'actor' => 'https://example.com/actor/test']);

            // Sign the request
            $headers = HTTPSignature::sign($privateKey, $keyId, $url, $body);

            $this->assertNotEmpty($headers, 'Sign should return non-empty headers');

            // Parse headers into associative array for verification
            $headerMap = [];
            foreach ($headers as $header) {
                $parts = explode(': ', $header, 2);
                if (count($parts) === 2) {
                    $headerMap[strtolower($parts[0])] = $parts[1];
                }
            }

            $this->assertArrayHasKey('signature', $headerMap);
            $this->assertArrayHasKey('digest', $headerMap);
            $this->assertArrayHasKey('date', $headerMap);

            // The sign() method includes 'host' in the signing string but does
            // not emit it as a standalone HTTP header (the HTTP client sets it).
            // We must add it to the headerMap for verify() to reconstruct the
            // signing string correctly.
            $parsed = parse_url($url);
            $headerMap['host'] = $parsed['host'];

            // Verify the signature
            $result = HTTPSignature::verify(
                $publicKey,
                $headerMap,
                $body,
                'POST',
                '/inbox'
            );

            $this->assertTrue($result, 'Signature verification should succeed for a correctly signed request');
        }

        /**
         * Test that verify rejects a tampered body (digest mismatch).
         */
        public function testSignatureRejectsTamperedBody()
        {
            // This test requires the Idno framework for logging calls in verify()
            if (!\Idno\Core\Idno::site()) {
                $this->markTestSkipped('Requires Idno framework for logging.');
            }

            $config = [
                'digest_alg'       => 'sha512',
                'private_key_bits' => 2048,
                'private_key_type' => OPENSSL_KEYTYPE_RSA,
            ];
            $key = openssl_pkey_new($config);
            $privateKey = '';
            openssl_pkey_export($key, $privateKey);
            $detail = openssl_pkey_get_details($key);
            $publicKey = $detail['key'];

            $keyId = 'https://example.com/actor/test#main-key';
            $url = 'https://remote.example/inbox';
            $body = json_encode(['type' => 'Create']);

            $headers = HTTPSignature::sign($privateKey, $keyId, $url, $body);
            $headerMap = [];
            foreach ($headers as $header) {
                $parts = explode(': ', $header, 2);
                if (count($parts) === 2) {
                    $headerMap[strtolower($parts[0])] = $parts[1];
                }
            }
            $headerMap['host'] = 'remote.example';

            // Tamper with the body
            $tamperedBody = json_encode(['type' => 'Delete']);

            $result = HTTPSignature::verify(
                $publicKey,
                $headerMap,
                $tamperedBody,
                'POST',
                '/inbox'
            );

            $this->assertFalse($result, 'Signature verification should fail for tampered body');
        }

        /**
         * Test that signGet produces valid headers for GET requests.
         */
        public function testSignGetProducesHeaders()
        {
            $config = [
                'digest_alg'       => 'sha512',
                'private_key_bits' => 2048,
                'private_key_type' => OPENSSL_KEYTYPE_RSA,
            ];
            $key = openssl_pkey_new($config);
            $privateKey = '';
            openssl_pkey_export($key, $privateKey);

            $keyId = 'https://example.com/actor/test#main-key';
            $url = 'https://remote.example/actor/alice';

            $headers = HTTPSignature::signGet($privateKey, $keyId, $url);

            $this->assertNotEmpty($headers);

            // Parse headers
            $headerMap = [];
            foreach ($headers as $header) {
                $parts = explode(': ', $header, 2);
                if (count($parts) === 2) {
                    $headerMap[strtolower($parts[0])] = $parts[1];
                }
            }

            $this->assertArrayHasKey('signature', $headerMap);
            $this->assertArrayHasKey('date', $headerMap);
            $this->assertArrayHasKey('accept', $headerMap);
        }

        /**
         * Test signGet + verify round-trip (simulates signed actor fetch).
         */
        public function testSignGetAndVerifyRoundtrip()
        {
            $config = [
                'digest_alg'       => 'sha512',
                'private_key_bits' => 2048,
                'private_key_type' => OPENSSL_KEYTYPE_RSA,
            ];
            $key = openssl_pkey_new($config);
            $privateKey = '';
            openssl_pkey_export($key, $privateKey);
            $detail = openssl_pkey_get_details($key);
            $publicKey = $detail['key'];

            $keyId = 'https://example.com/actor/test#main-key';
            $url = 'https://remote.example/actor/alice';

            // Sign the GET request
            $headers = HTTPSignature::signGet($privateKey, $keyId, $url);
            $this->assertNotEmpty($headers);

            // Parse into headerMap
            $headerMap = [];
            foreach ($headers as $header) {
                $parts = explode(': ', $header, 2);
                if (count($parts) === 2) {
                    $headerMap[strtolower($parts[0])] = $parts[1];
                }
            }

            // Add host (signGet includes it in the signing string but not as a returned header)
            $headerMap['host'] = 'remote.example';

            // Verify — signGet uses (request-target), host, date
            $result = HTTPSignature::verify(
                $publicKey,
                $headerMap,
                '',          // GET requests have no body
                'GET',
                '/actor/alice'
            );

            $this->assertTrue($result, 'signGet signature should verify correctly');
        }

        /**
         * Test that verify rejects a signature made with a different key.
         */
        public function testSignatureRejectsWrongKey()
        {
            $config = [
                'digest_alg'       => 'sha512',
                'private_key_bits' => 2048,
                'private_key_type' => OPENSSL_KEYTYPE_RSA,
            ];

            // Sign with key 1
            $key1 = openssl_pkey_new($config);
            $privateKey1 = '';
            openssl_pkey_export($key1, $privateKey1);

            // Verify with key 2
            $key2 = openssl_pkey_new($config);
            $detail2 = openssl_pkey_get_details($key2);
            $publicKey2 = $detail2['key'];

            $keyId = 'https://example.com/actor/test#main-key';
            $url = 'https://remote.example/inbox';
            $body = json_encode(['type' => 'Create']);

            $headers = HTTPSignature::sign($privateKey1, $keyId, $url, $body);
            $headerMap = [];
            foreach ($headers as $header) {
                $parts = explode(': ', $header, 2);
                if (count($parts) === 2) {
                    $headerMap[strtolower($parts[0])] = $parts[1];
                }
            }
            $headerMap['host'] = 'remote.example';

            $result = HTTPSignature::verify(
                $publicKey2,
                $headerMap,
                $body,
                'POST',
                '/inbox'
            );

            $this->assertFalse($result, 'Signature verification should fail with wrong public key');
        }

        // -----------------------------------------------------------------
        // Signature header parsing
        // -----------------------------------------------------------------

        /**
         * Test parseSignatureHeader extracts keyId, algorithm, headers, and signature.
         */
        public function testParseSignatureHeader()
        {
            $header = 'keyId="https://example.com/actor/alice#main-key",algorithm="rsa-sha256",headers="(request-target) host date digest content-type",signature="abc123def456=="';

            $parsed = HTTPSignature::parseSignatureHeader($header);

            $this->assertIsArray($parsed);
            $this->assertEquals('https://example.com/actor/alice#main-key', $parsed['keyId']);
            $this->assertEquals('rsa-sha256', $parsed['algorithm']);
            $this->assertEquals('(request-target) host date digest content-type', $parsed['headers']);
            $this->assertEquals('abc123def456==', $parsed['signature']);
        }

        /**
         * Test parseSignatureHeader returns false for incomplete header.
         */
        public function testParseSignatureHeaderIncomplete()
        {
            // This test requires the Idno framework for logging calls
            if (!\Idno\Core\Idno::site()) {
                $this->markTestSkipped('Requires Idno framework for logging.');
            }

            // Missing required fields
            $header = 'keyId="https://example.com/key"';

            $parsed = HTTPSignature::parseSignatureHeader($header);

            $this->assertFalse($parsed);
        }
    }

}
