<?php

namespace IdnoPlugins\ActivityPub\Pages;

use Idno\Common\Entity;
use Idno\Entities\User;
use IdnoPlugins\ActivityPub\ActivityBuilder;
use IdnoPlugins\ActivityPub\Entities\ActivityPubFollower;

/**
 * ActivityPub actor profile endpoint.
 * Route: /actor/{handle}
 * Returns a Person object for the given local user.
 */
class Actor extends \Idno\Common\Page
{

    function getContent()
    {
        $handle = $this->arguments[0] ?? '';
        $user = User::getByHandle($handle);

        if (!$user) {
            $this->noContent();
            return;
        }

        $actorId = $user->getActivityPubActorID();
        $followersCount = ActivityPubFollower::getFollowerCount($user->getUUID());
        $statusesCount = self::getContentCount($user);

        $person = [
            '@context' => [
                'https://www.w3.org/ns/activitystreams',
                'https://w3id.org/security/v1',
                [
                    'toot'         => 'http://joinmastodon.org/ns#',
                    'discoverable' => 'toot:discoverable',
                ],
            ],
            'id'                        => $actorId,
            'type'                      => 'Person',
            'url'                       => $user->getURL(),
            'preferredUsername'          => $user->getHandle(),
            'name'                      => $user->getName(),
            'summary'                   => $user->getDescription(),
            'manuallyApprovesFollowers' => false,
            'discoverable'              => true,
            'inbox'                     => $actorId . '/inbox',
            'outbox'                    => $actorId . '/outbox',
            'followers'                 => $actorId . '/followers',
            'following'                 => $actorId . '/following',
            'publicKey'                 => $user->getPublicKey(),
            'endpoints'                 => $user->getActivityPubEndpoints(),
            'followersCount'            => $followersCount,
            'followingCount'            => 0,
            'statusesCount'             => $statusesCount,
        ];

        $icon = $user->getIcon();
        if ($icon) {
            $mime = \Idno\Common\Entity::getMediaMimeType($icon);
            $person['icon'] = [
                'type'      => 'Image',
                'url'       => $icon,
                'mediaType' => $mime ?: 'image/png',
            ];
        }

        header('Content-Type: application/activity+json');
        header('Access-Control-Allow-Origin: *');
        echo json_encode($person, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        exit;
    }

    function postContent()
    {
    }

    /**
     * Count public published content entities for a user, excluding non-content entities.
     *
     * @param User $user
     * @return int
     */
    private static function getContentCount(User $user): int
    {
        return (int) Entity::countFromX(ActivityBuilder::NON_CONTENT_SUBTYPES, [
            'owner'          => $user->getUUID(),
            'publish_status' => 'published',
            'access'         => 'PUBLIC',
        ]);
    }
}
