<?php

namespace IdnoPlugins\ActivityPub;

use Idno\Core\Webservice;

/**
 * Fetches and parses remote ActivityPub actor profiles.
 */
class RemoteActor
{

    /**
     * Fetch a remote actor's profile by their ActivityPub ID.
     *
     * @param string $actorUri The remote actor's ID URI
     * @return array|false Structured actor data, or false on failure
     */
    public static function fetch(string $actorUri)
    {
        if (empty($actorUri) || !filter_var($actorUri, FILTER_VALIDATE_URL)) {
            \Idno\Core\Idno::site()->logging()->warning('ActivityPub: Invalid actor URI: ' . $actorUri);
            return false;
        }

        try {
            $response = Webservice::get($actorUri, null, [
                'Accept: application/activity+json, application/ld+json; profile="https://www.w3.org/ns/activitystreams"',
            ]);

            if (empty($response['content']) || $response['response'] < 200 || $response['response'] >= 300) {
                \Idno\Core\Idno::site()->logging()->warning('ActivityPub: Failed to fetch actor ' . $actorUri . ' (HTTP ' . ($response['response'] ?? 'unknown') . ')');
                return false;
            }

            $data = json_decode($response['content'], true);
            if (empty($data) || empty($data['id'])) {
                \Idno\Core\Idno::site()->logging()->warning('ActivityPub: Invalid actor document from ' . $actorUri);
                return false;
            }

            // Extract shared inbox from endpoints
            $sharedInbox = null;
            if (!empty($data['endpoints']['sharedInbox'])) {
                $sharedInbox = $data['endpoints']['sharedInbox'];
            }

            // Extract icon URL
            $icon = null;
            if (!empty($data['icon'])) {
                if (is_string($data['icon'])) {
                    $icon = $data['icon'];
                } elseif (is_array($data['icon']) && !empty($data['icon']['url'])) {
                    $icon = $data['icon']['url'];
                }
            }

            // Build preferred handle
            $handle = $data['preferredUsername'] ?? '';
            $parsedUri = parse_url($data['id']);
            if ($handle && !empty($parsedUri['host'])) {
                $handle = $handle . '@' . $parsedUri['host'];
            }

            return [
                'id'            => $data['id'],
                'inbox'         => $data['inbox'] ?? null,
                'sharedInbox'   => $sharedInbox,
                'outbox'        => $data['outbox'] ?? null,
                'preferredUsername' => $data['preferredUsername'] ?? null,
                'name'          => $data['name'] ?? $data['preferredUsername'] ?? null,
                'handle'        => $handle,
                'icon'          => $icon,
                'summary'       => $data['summary'] ?? null,
                'url'           => $data['url'] ?? $data['id'],
                'publicKey'     => $data['publicKey'] ?? null,
                'followers'     => $data['followers'] ?? null,
                'following'     => $data['following'] ?? null,
            ];

        } catch (\Exception $e) {
            \Idno\Core\Idno::site()->logging()->error('ActivityPub: Exception fetching actor ' . $actorUri . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch the public key for a remote actor given a key ID.
     * The key ID is typically in the format "actorUri#main-key".
     *
     * @param string $keyId The public key ID from the Signature header
     * @return string|false PEM-encoded public key, or false on failure
     */
    public static function fetchPublicKey(string $keyId)
    {
        // Strip the fragment to get the actor URI
        $actorUri = preg_replace('/#.*$/', '', $keyId);

        $actorData = self::fetch($actorUri);
        if (!$actorData || empty($actorData['publicKey'])) {
            return false;
        }

        $publicKey = $actorData['publicKey'];

        // Verify the key ID matches
        if (!empty($publicKey['id']) && $publicKey['id'] !== $keyId) {
            // Key ID mismatch - the actor may have rotated keys.
            // Still return the key if the owner matches.
            if (empty($publicKey['owner']) || $publicKey['owner'] !== $actorUri) {
                \Idno\Core\Idno::site()->logging()->warning('ActivityPub: Key owner mismatch for ' . $keyId);
                return false;
            }
        }

        return $publicKey['publicKeyPem'] ?? false;
    }
}
