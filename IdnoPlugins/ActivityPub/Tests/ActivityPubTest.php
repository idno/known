<?php

namespace IdnoPlugins\ActivityPub\Tests {

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
