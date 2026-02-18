<?php

namespace IdnoPlugins\ActivityPub;

use Idno\Common\Entity;
use Idno\Entities\User;

/**
 * Builds ActivityPub activity objects for outbound federation.
 * Uses plain arrays rather than the ActivityPhp library for full control over outgoing activities.
 */
class ActivityBuilder
{

    const CONTEXT = [
        'https://www.w3.org/ns/activitystreams',
        'https://w3id.org/security/v1',
    ];

    const PUBLIC_AUDIENCE = 'https://www.w3.org/ns/activitystreams#Public';

    /**
     * Map of Idno activity streams types to ActivityPub object types.
     */
    const TYPE_MAP = [
        'note'     => 'Note',
        'article'  => 'Article',
        'image'    => 'Note',  // Images federate as Notes with attachments
        'event'    => 'Event',
        'place'    => 'Note',  // Checkins federate as Notes with location
        'bookmark' => 'Note',
        'rsvp'     => 'Note',
        'media'    => 'Note',
        'entity'   => 'Note',
    ];

    /**
     * Convert an Idno entity to an ActivityPub object (Note, Article, etc.).
     *
     * @param Entity $entity The Idno entity
     * @return array ActivityPub object
     */
    public static function buildObject(Entity $entity): array
    {
        $idnoType = $entity->getActivityStreamsObjectType();
        $apType = self::TYPE_MAP[$idnoType] ?? 'Note';
        $owner = $entity->getOwner();

        $object = [
            'id'           => $entity->getUUID(),
            'type'         => $apType,
            'url'          => $entity->getURL(),
            'attributedTo' => $entity->getActivityPubActorID(),
            'published'    => date(\DateTime::RFC3339, $entity->created),
            'to'           => $entity->getAddressedTo(),
            'cc'           => [],
        ];

        // Add followers collection to cc for public content
        if ($owner && $entity->isPublic()) {
            $object['cc'][] = $owner->getActivityPubFollowersURL();
        }

        // Content handling varies by type
        if ($apType === 'Article') {
            $object['name'] = $entity->getTitle();
            $object['content'] = $entity->getFormattedContent();
        } elseif ($idnoType === 'image') {
            // For images, use the title as content and attach the image
            $object['content'] = $entity->getTitle();
        } else {
            $object['content'] = $entity->getFormattedContent();
        }

        // Updated time
        if ($entity->updated && $entity->updated !== $entity->created) {
            $object['updated'] = date(\DateTime::RFC3339, $entity->updated);
        }

        // Tags/hashtags
        $tags = $entity->getHashTagObjects();
        if (!empty($tags)) {
            $object['tag'] = $tags;
        }

        // Attachments (images, files)
        $attachments = $entity->getFormattedAttachments();
        if (!empty($attachments)) {
            $object['attachment'] = $attachments;
        }

        // Reply handling
        if ($entity->isReply()) {
            $replyUrls = $entity->getReplyToURLs();
            if (!empty($replyUrls)) {
                $object['inReplyTo'] = is_array($replyUrls) ? $replyUrls[0] : $replyUrls;
            }
        }

        // Location data (for checkins)
        if ($idnoType === 'place' || !empty($entity->lat)) {
            if (!empty($entity->lat) && !empty($entity->long)) {
                $location = [
                    'type' => 'Place',
                ];
                if (!empty($entity->placename)) {
                    $location['name'] = $entity->placename;
                }
                $location['latitude'] = (float)$entity->lat;
                $location['longitude'] = (float)$entity->long;
                if (!empty($entity->address)) {
                    $location['name'] = $location['name'] ?? $entity->address;
                }
                $object['location'] = $location;
            }
        }

        return $object;
    }

    /**
     * Build a Create activity wrapping an entity.
     *
     * @param Entity $entity The entity being created
     * @return array ActivityPub Create activity
     */
    public static function buildCreate(Entity $entity): array
    {
        $object = self::buildObject($entity);
        $owner = $entity->getOwner();

        return self::wrapActivity('Create', $owner, $object, $entity->getUUID() . '#create');
    }

    /**
     * Build an Update activity wrapping an entity.
     *
     * @param Entity $entity The entity being updated
     * @return array ActivityPub Update activity
     */
    public static function buildUpdate(Entity $entity): array
    {
        $object = self::buildObject($entity);
        $owner = $entity->getOwner();

        return self::wrapActivity('Update', $owner, $object, $entity->getUUID() . '#update-' . time());
    }

    /**
     * Build a Delete activity for an entity.
     *
     * @param Entity $entity The entity being deleted
     * @return array ActivityPub Delete activity with Tombstone object
     */
    public static function buildDelete(Entity $entity): array
    {
        $idnoType = $entity->getActivityStreamsObjectType();
        $apType = self::TYPE_MAP[$idnoType] ?? 'Note';
        $owner = $entity->getOwner();

        $tombstone = [
            'id'         => $entity->getUUID(),
            'type'       => 'Tombstone',
            'formerType' => $apType,
        ];

        $activity = self::wrapActivity('Delete', $owner, $tombstone, $entity->getUUID() . '#delete');

        // Deletions should be addressed to public + followers
        if ($owner) {
            $activity['to'] = [self::PUBLIC_AUDIENCE];
            $activity['cc'] = [$owner->getActivityPubFollowersURL()];
        }

        return $activity;
    }

    /**
     * Build an Accept activity in response to a Follow.
     *
     * @param array $followActivity The original Follow activity being accepted
     * @param User  $user           The local user accepting the follow
     * @return array ActivityPub Accept activity
     */
    public static function buildAccept(array $followActivity, User $user): array
    {
        $actorId = $user->getActivityPubActorID();

        return [
            '@context'  => self::CONTEXT,
            'id'        => $actorId . '#accept-' . md5($followActivity['id'] ?? time()),
            'type'      => 'Accept',
            'actor'     => $actorId,
            'object'    => $followActivity,
            'published' => date(\DateTime::RFC3339),
        ];
    }

    /**
     * Wrap an object in an activity.
     *
     * @param string    $type   Activity type (Create, Update, Delete, etc.)
     * @param User|null $actor  The actor performing the activity
     * @param array     $object The object of the activity
     * @param string    $id     Activity ID
     * @return array ActivityPub activity
     */
    public static function wrapActivity(string $type, ?User $actor, array $object, string $id = ''): array
    {
        $actorId = $actor ? $actor->getActivityPubActorID() : '';

        $activity = [
            '@context'  => self::CONTEXT,
            'id'        => $id ?: ($actorId . '#activity-' . md5(time() . mt_rand())),
            'type'      => $type,
            'actor'     => $actorId,
            'published' => date(\DateTime::RFC3339),
            'object'    => $object,
        ];

        // Inherit addressing from object if available
        if (!empty($object['to'])) {
            $activity['to'] = $object['to'];
        }
        if (!empty($object['cc'])) {
            $activity['cc'] = $object['cc'];
        }

        return $activity;
    }
}
