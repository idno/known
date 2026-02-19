<?php

namespace IdnoPlugins\ActivityPub\Pages;

use Idno\Entities\User;

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

        $person = [
            '@context' => [
                'https://www.w3.org/ns/activitystreams',
                'https://w3id.org/security/v1',
            ],
            'id'                        => $actorId,
            'type'                      => 'Person',
            'url'                       => $user->getURL(),
            'preferredUsername'          => $user->getHandle(),
            'name'                      => $user->getName(),
            'summary'                   => $user->getDescription(),
            'manuallyApprovesFollowers' => false,
            'inbox'                     => $actorId . '/inbox',
            'outbox'                    => $actorId . '/outbox',
            'followers'                 => $actorId . '/followers',
            'following'                 => $actorId . '/following',
            'publicKey'                 => $user->getPublicKey(),
            'endpoints'                 => $user->getActivityPubEndpoints(),
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
        echo json_encode($person, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    function postContent()
    {
    }
}
