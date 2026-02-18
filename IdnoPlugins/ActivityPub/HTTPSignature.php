<?php

namespace IdnoPlugins\ActivityPub;

/**
 * HTTP Signature utilities for ActivityPub federation.
 * Implements draft-cavage-http-signatures for request signing and verification.
 */
class HTTPSignature
{

    /**
     * Sign an outbound HTTP request for ActivityPub delivery.
     *
     * @param string $privateKey PEM-encoded private key
     * @param string $keyId      Public key ID (e.g., https://example.com/actor/user#main-key)
     * @param string $url        Target inbox URL
     * @param string $body       JSON-encoded request body
     * @param string $method     HTTP method (default: POST)
     * @return array Headers array ready for Webservice::post()
     */
    public static function sign(string $privateKey, string $keyId, string $url, string $body, string $method = 'POST'): array
    {
        $parsed = parse_url($url);
        $host = $parsed['host'];
        if (!empty($parsed['port']) && $parsed['port'] != 443 && $parsed['port'] != 80) {
            $host .= ':' . $parsed['port'];
        }
        $path = $parsed['path'] ?? '/';
        if (!empty($parsed['query'])) {
            $path .= '?' . $parsed['query'];
        }

        $date = gmdate('D, d M Y H:i:s T');
        $digest = 'SHA-256=' . base64_encode(hash('sha256', $body, true));
        $contentType = 'application/ld+json; profile="https://www.w3.org/ns/activitystreams"';

        $signingHeaders = [
            '(request-target)' => strtolower($method) . ' ' . $path,
            'host'             => $host,
            'date'             => $date,
            'digest'           => $digest,
            'content-type'     => $contentType,
        ];

        $signingString = '';
        $headerNames = [];
        foreach ($signingHeaders as $name => $value) {
            if (!empty($signingString)) {
                $signingString .= "\n";
            }
            $signingString .= $name . ': ' . $value;
            $headerNames[] = $name;
        }

        $signature = '';
        $success = openssl_sign($signingString, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        if (!$success) {
            \Idno\Core\Idno::site()->logging()->error('ActivityPub: Failed to sign request to ' . $url);
            return [];
        }

        $signatureB64 = base64_encode($signature);
        $signatureHeader = sprintf(
            'keyId="%s",algorithm="rsa-sha256",headers="%s",signature="%s"',
            $keyId,
            implode(' ', $headerNames),
            $signatureB64
        );

        return [
            'Date: ' . $date,
            'Digest: ' . $digest,
            'Content-Type: ' . $contentType,
            'Signature: ' . $signatureHeader,
            'Accept: application/ld+json; profile="https://www.w3.org/ns/activitystreams"',
        ];
    }

    /**
     * Sign a GET request (used for fetching remote actors).
     *
     * @param string $privateKey PEM-encoded private key
     * @param string $keyId      Public key ID
     * @param string $url        Target URL
     * @return array Headers array
     */
    public static function signGet(string $privateKey, string $keyId, string $url): array
    {
        $parsed = parse_url($url);
        $host = $parsed['host'];
        if (!empty($parsed['port']) && $parsed['port'] != 443 && $parsed['port'] != 80) {
            $host .= ':' . $parsed['port'];
        }
        $path = $parsed['path'] ?? '/';
        if (!empty($parsed['query'])) {
            $path .= '?' . $parsed['query'];
        }

        $date = gmdate('D, d M Y H:i:s T');

        $signingHeaders = [
            '(request-target)' => 'get ' . $path,
            'host'             => $host,
            'date'             => $date,
        ];

        $signingString = '';
        $headerNames = [];
        foreach ($signingHeaders as $name => $value) {
            if (!empty($signingString)) {
                $signingString .= "\n";
            }
            $signingString .= $name . ': ' . $value;
            $headerNames[] = $name;
        }

        $signature = '';
        openssl_sign($signingString, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        $signatureB64 = base64_encode($signature);
        $signatureHeader = sprintf(
            'keyId="%s",algorithm="rsa-sha256",headers="%s",signature="%s"',
            $keyId,
            implode(' ', $headerNames),
            $signatureB64
        );

        return [
            'Date: ' . $date,
            'Signature: ' . $signatureHeader,
            'Accept: application/activity+json',
        ];
    }

    /**
     * Verify an incoming HTTP signature.
     *
     * @param string $publicKeyPem PEM-encoded public key of the remote actor
     * @param array  $headers      Request headers (lowercase keys)
     * @param string $body         Raw request body
     * @param string $method       HTTP method
     * @param string $path         Request path (including query string)
     * @return bool True if signature is valid
     */
    public static function verify(string $publicKeyPem, array $headers, string $body, string $method, string $path): bool
    {
        // Check Date header is within acceptable window (±12 hours)
        if (!empty($headers['date'])) {
            $requestTime = strtotime($headers['date']);
            if ($requestTime === false || abs(time() - $requestTime) > 43200) {
                \Idno\Core\Idno::site()->logging()->warning('ActivityPub: Signature date out of range');
                return false;
            }
        }

        // Verify Digest header matches body
        if (!empty($headers['digest'])) {
            $expectedDigest = 'SHA-256=' . base64_encode(hash('sha256', $body, true));
            if ($headers['digest'] !== $expectedDigest) {
                \Idno\Core\Idno::site()->logging()->warning('ActivityPub: Digest mismatch');
                return false;
            }
        }

        // Parse the Signature header
        if (empty($headers['signature'])) {
            \Idno\Core\Idno::site()->logging()->warning('ActivityPub: No Signature header');
            return false;
        }

        $sigParams = self::parseSignatureHeader($headers['signature']);
        if (!$sigParams) {
            return false;
        }

        // Reconstruct the signing string
        $signingString = '';
        $signedHeaders = explode(' ', $sigParams['headers']);
        foreach ($signedHeaders as $headerName) {
            if (!empty($signingString)) {
                $signingString .= "\n";
            }
            if ($headerName === '(request-target)') {
                $signingString .= '(request-target): ' . strtolower($method) . ' ' . $path;
            } else {
                $headerValue = $headers[strtolower($headerName)] ?? '';
                $signingString .= strtolower($headerName) . ': ' . $headerValue;
            }
        }

        $decodedSignature = base64_decode($sigParams['signature']);
        if ($decodedSignature === false) {
            return false;
        }

        $result = openssl_verify($signingString, $decodedSignature, $publicKeyPem, OPENSSL_ALGO_SHA256);

        return $result === 1;
    }

    /**
     * Parse the Signature header into its components.
     *
     * @param string $header The Signature header value
     * @return array|false Parsed components or false on failure
     */
    public static function parseSignatureHeader(string $header)
    {
        $params = [];
        // Match key="value" pairs
        if (preg_match_all('/(\w+)="([^"]*)"/', $header, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $params[$match[1]] = $match[2];
            }
        }

        if (empty($params['keyId']) || empty($params['signature']) || empty($params['headers'])) {
            \Idno\Core\Idno::site()->logging()->warning('ActivityPub: Incomplete Signature header');
            return false;
        }

        return $params;
    }

    /**
     * Extract request headers into a normalized associative array.
     *
     * @return array Headers with lowercase keys
     */
    public static function getRequestHeaders(): array
    {
        $headers = [];

        if (function_exists('getallheaders')) {
            foreach (getallheaders() as $name => $value) {
                $headers[strtolower($name)] = $value;
            }
        } else {
            // Fallback for servers that don't support getallheaders()
            foreach ($_SERVER as $key => $value) {
                if (substr($key, 0, 5) === 'HTTP_') {
                    $headerName = strtolower(str_replace('_', '-', substr($key, 5)));
                    $headers[$headerName] = $value;
                }
            }
            if (isset($_SERVER['CONTENT_TYPE'])) {
                $headers['content-type'] = $_SERVER['CONTENT_TYPE'];
            }
            if (isset($_SERVER['CONTENT_LENGTH'])) {
                $headers['content-length'] = $_SERVER['CONTENT_LENGTH'];
            }
        }

        return $headers;
    }
}
