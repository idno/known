<?php

namespace IdnoPlugins\ActivityPub;

use Idno\Entities\User;
use IdnoPlugins\ActivityPub\Entities\ActivityPubFollower;

/**
 * Processes incoming ActivityPub activities received at inbox endpoints.
 * All processing is synchronous as per the requirements.
 */
class ActivityHandler
{

    /**
     * Handle an incoming activity.
     *
     * @param string      $rawBody   Raw JSON request body
     * @param array       $headers   Normalized request headers (lowercase keys)
     * @param string      $method    HTTP method
     * @param string      $path      Request path
     * @param string|null $targetHandle Optional: the handle of the target user (for per-user inbox)
     * @return array ['status' => HTTP status code, 'body' => response body]
     */
    public static function handle(string $rawBody, array $headers, string $method, string $path, ?string $targetHandle = null): array
    {
        // Parse the activity
        $activity = json_decode($rawBody, true);
        if (empty($activity) || empty($activity['type'])) {
            return ['status' => 400, 'body' => json_encode(['error' => 'Invalid activity'])];
        }

        \Idno\Core\Idno::site()->logging()->debug('ActivityPub: Received ' . $activity['type'] . ' activity from ' . ($activity['actor'] ?? 'unknown'));

        // Verify HTTP signature
        if (!self::verifySignature($headers, $rawBody, $method, $path, $activity)) {
            return ['status' => 401, 'body' => json_encode(['error' => 'Invalid signature'])];
        }

        // Route by activity type
        $type = $activity['type'];
        switch ($type) {
            case 'Follow':
                return self::handleFollow($activity, $targetHandle);

            case 'Undo':
                return self::handleUndo($activity);

            case 'Delete':
                return self::handleDelete($activity);

            case 'Accept':
                // We don't currently initiate follows, so just acknowledge
                return ['status' => 200, 'body' => ''];

            case 'Reject':
                // Acknowledged but not acted on for now
                return ['status' => 200, 'body' => ''];

            default:
                \Idno\Core\Idno::site()->logging()->debug('ActivityPub: Unhandled activity type: ' . $type);
                return ['status' => 202, 'body' => ''];
        }
    }

    /**
     * Verify the HTTP signature on an incoming request.
     *
     * @param array  $headers  Normalized request headers
     * @param string $body     Raw request body
     * @param string $method   HTTP method
     * @param string $path     Request path
     * @param array  $activity Parsed activity (for actor fallback)
     * @return bool
     */
    private static function verifySignature(array $headers, string $body, string $method, string $path, array $activity): bool
    {
        if (empty($headers['signature'])) {
            \Idno\Core\Idno::site()->logging()->warning('ActivityPub: No signature on incoming request');
            return false;
        }

        // Parse the signature to get keyId
        $sigParams = HTTPSignature::parseSignatureHeader($headers['signature']);
        if (!$sigParams || empty($sigParams['keyId'])) {
            return false;
        }

        // Try to resolve the local target user for signed key fetching.
        // This is needed for remote instances that require authorized fetch.
        $localUser = null;
        $object = $activity['object'] ?? '';
        $targetActorId = is_string($object) ? $object : ($object['id'] ?? '');
        if (!empty($targetActorId)) {
            $localUser = self::resolveLocalUser($targetActorId);
            if ($localUser === false) {
                $localUser = null;
            }
        }

        // Fetch the remote actor's public key (using signed GET when possible)
        $publicKey = RemoteActor::fetchPublicKey($sigParams['keyId'], $localUser);
        if (!$publicKey) {
            \Idno\Core\Idno::site()->logging()->warning('ActivityPub: Could not fetch public key for ' . $sigParams['keyId']);
            return false;
        }

        return HTTPSignature::verify($publicKey, $headers, $body, $method, $path);
    }

    /**
     * Handle an incoming Follow activity.
     *
     * @param array       $activity     The Follow activity
     * @param string|null $targetHandle Expected target user handle (from URL)
     * @return array
     */
    private static function handleFollow(array $activity, ?string $targetHandle = null): array
    {
        $actorUri = $activity['actor'] ?? '';
        $object = $activity['object'] ?? '';

        // The object of a Follow is the actor being followed (our user)
        if (empty($actorUri) || empty($object)) {
            return ['status' => 400, 'body' => json_encode(['error' => 'Missing actor or object'])];
        }

        // Resolve the local user being followed
        $targetActorId = is_string($object) ? $object : ($object['id'] ?? '');
        $user = self::resolveLocalUser($targetActorId);

        if (!$user) {
            \Idno\Core\Idno::site()->logging()->warning('ActivityPub: Follow target not found: ' . $targetActorId);
            return ['status' => 404, 'body' => json_encode(['error' => 'User not found'])];
        }

        // Validate against expected handle if provided
        if ($targetHandle && strtolower($user->getHandle()) !== strtolower($targetHandle)) {
            return ['status' => 400, 'body' => json_encode(['error' => 'Target mismatch'])];
        }

        // Check for existing follower record (idempotent)
        $existing = ActivityPubFollower::getByActorAndUser($actorUri, $user->getUUID());
        if ($existing) {
            \Idno\Core\Idno::site()->logging()->debug('ActivityPub: Duplicate Follow from ' . $actorUri);
            // Still send Accept for idempotency — use the stored inbox URL, not the actor URI
            $inbox = $existing->actor_inbox ?: $actorUri;
            self::sendAccept($activity, $user, $inbox);
            return ['status' => 200, 'body' => ''];
        }

        // Fetch remote actor data
        $actorData = RemoteActor::fetch($actorUri);
        if (!$actorData) {
            \Idno\Core\Idno::site()->logging()->warning('ActivityPub: Could not resolve remote actor: ' . $actorUri);
            return ['status' => 400, 'body' => json_encode(['error' => 'Could not resolve actor'])];
        }

        // Create follower record
        $follower = new ActivityPubFollower();
        $follower->actor_uri = $actorData['id'];
        $follower->actor_inbox = $actorData['inbox'];
        $follower->actor_shared_inbox = $actorData['sharedInbox'];
        $follower->user_uuid = $user->getUUID();
        $follower->actor_handle = $actorData['handle'];
        $follower->actor_name = $actorData['name'];
        $follower->actor_icon = $actorData['icon'];
        $follower->actor_public_key = $actorData['publicKey']['publicKeyPem'] ?? null;
        $follower->follow_activity_id = $activity['id'] ?? '';
        $follower->setAccess('PUBLIC');

        if (!$follower->save(true)) {
            \Idno\Core\Idno::site()->logging()->error('ActivityPub: Failed to save follower record for ' . $actorUri);
            return ['status' => 500, 'body' => json_encode(['error' => 'Internal error'])];
        }

        \Idno\Core\Idno::site()->logging()->info('ActivityPub: New follower ' . $actorData['handle'] . ' for user ' . $user->getHandle());

        // Auto-accept: queue Accept delivery
        self::sendAccept($activity, $user, $actorData['inbox']);

        return ['status' => 202, 'body' => ''];
    }

    /**
     * Send an Accept activity in response to a Follow.
     *
     * @param array  $followActivity The original Follow activity
     * @param User   $user           The local user accepting the follow
     * @param string $targetInbox    The remote actor's inbox URL
     */
    private static function sendAccept(array $followActivity, User $user, string $targetInbox): void
    {
        $acceptActivity = ActivityBuilder::buildAccept($followActivity, $user);

        // Queue Accept delivery via async pipeline
        \Idno\Core\Idno::site()->queue()->enqueue('default', 'activitypub/deliver', [
            'user_uuid' => $user->getUUID(),
            'activity'  => $acceptActivity,
            'inbox'     => $targetInbox,
        ]);
    }

    /**
     * Handle an incoming Undo activity.
     *
     * @param array $activity The Undo activity
     * @return array
     */
    private static function handleUndo(array $activity): array
    {
        $actorUri = $activity['actor'] ?? '';
        $object = $activity['object'] ?? [];

        if (empty($actorUri)) {
            return ['status' => 400, 'body' => json_encode(['error' => 'Missing actor'])];
        }

        // Determine what's being undone
        $objectType = '';
        if (is_string($object)) {
            // Object is just an ID - we need to figure out what it is
            // Most commonly this is an Undo of a Follow
            $objectType = 'Follow'; // Assume Follow for now
        } elseif (is_array($object)) {
            $objectType = $object['type'] ?? '';
        }

        if ($objectType === 'Follow') {
            return self::handleUndoFollow($actorUri, $object);
        }

        \Idno\Core\Idno::site()->logging()->debug('ActivityPub: Unhandled Undo type: ' . $objectType);
        return ['status' => 200, 'body' => ''];
    }

    /**
     * Handle an Undo Follow (unfollow).
     *
     * @param string       $actorUri The actor who is unfollowing
     * @param array|string $object   The Follow activity being undone
     * @return array
     */
    private static function handleUndoFollow(string $actorUri, $object): array
    {
        // Determine the target user
        $targetActorId = '';
        if (is_array($object) && !empty($object['object'])) {
            $targetActorId = is_string($object['object']) ? $object['object'] : ($object['object']['id'] ?? '');
        }

        if (!empty($targetActorId)) {
            $user = self::resolveLocalUser($targetActorId);
            if ($user) {
                $follower = ActivityPubFollower::getByActorAndUser($actorUri, $user->getUUID());
                if ($follower) {
                    $follower->delete();
                    \Idno\Core\Idno::site()->logging()->info('ActivityPub: Unfollowed by ' . $actorUri . ' on user ' . $user->getHandle());
                }
            }
        } else {
            // No target specified - remove all follower records for this actor
            $count = ActivityPubFollower::removeByActor($actorUri);
            \Idno\Core\Idno::site()->logging()->info('ActivityPub: Removed ' . $count . ' follower record(s) for actor ' . $actorUri);
        }

        return ['status' => 200, 'body' => ''];
    }

    /**
     * Handle an incoming Delete activity.
     *
     * @param array $activity The Delete activity
     * @return array
     */
    private static function handleDelete(array $activity): array
    {
        $actorUri = $activity['actor'] ?? '';

        if (empty($actorUri)) {
            return ['status' => 400, 'body' => json_encode(['error' => 'Missing actor'])];
        }

        $object = $activity['object'] ?? '';
        $objectId = is_string($object) ? $object : ($object['id'] ?? '');

        // If the actor is deleting themselves
        if ($objectId === $actorUri) {
            $count = ActivityPubFollower::removeByActor($actorUri);
            \Idno\Core\Idno::site()->logging()->info('ActivityPub: Actor ' . $actorUri . ' deleted themselves, removed ' . $count . ' follower record(s)');
        }

        return ['status' => 200, 'body' => ''];
    }

    /**
     * Resolve a local user from an ActivityPub actor ID.
     *
     * @param string $actorId ActivityPub actor ID (e.g., https://example.com/actor/username)
     * @return User|false
     */
    private static function resolveLocalUser(string $actorId)
    {
        $siteUrl = \Idno\Core\Idno::site()->config()->getDisplayURL();

        // Try to extract handle from actor URL pattern: {siteUrl}actor/{handle}
        $actorPrefix = $siteUrl . 'actor/';
        if (strpos($actorId, $actorPrefix) === 0) {
            $handle = substr($actorId, strlen($actorPrefix));
            $handle = rtrim($handle, '/');
            return User::getByHandle($handle);
        }

        // Fallback: try to find user by iterating (less efficient)
        // This handles custom actor URLs
        if ($results = User::get([], [], PHP_INT_MAX)) {
            foreach ($results as $user) {
                if ($user->getActivityPubActorID() === $actorId) {
                    return $user;
                }
            }
        }

        return false;
    }
}
