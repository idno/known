<?php

    if (!class_exists('\ActivityPhp\Type')) return; // If we don't have the ActivityPub library, there's no point in continuing

    use \ActivityPhp\Type;

    header('Content-Type: application/ld+json; profile="https://www.w3.org/ns/activitystreams"');

    unset($vars['body']);

if (!empty($vars['exception'])) {
    $e = [
        'class' => get_class($vars['exception']),
        'message' => $vars['exception']->getMessage(),
        'file' => $vars['exception']->getFile(),
        'line' => $vars['exception']->getLine()
    ];
    $vars['exception'] = $e;
}

    /* @var \Idno\Common\Entity $object */

if ( isset($vars['user']) && 'person' === $vars['user']?->getActivityStreamsObjectType()) {

    $actorId = $vars['user']->getActivityPubActorID();

    $person = Type::create('Person', [
        '@context' => [
            'https://www.w3.org/ns/activitystreams',
            'https://w3id.org/security/v1',
        ],
        'id' => $actorId,
        'url' => ($vars['user']->getAuthorURL()),
        'preferredUsername' => $vars['user']->getHandle(),
        'name' => $vars['user']->getAuthorName(),
        'summary' => $vars['user']->getDescription(),
        'icon' => $vars['user']->getIconObject(),
        'inbox' => $actorId . '/inbox',
        'outbox' => $actorId . '/outbox',
        'followers' => $actorId . '/followers',
        'following' => $actorId . '/following',
        'publicKey' => $vars['user']->getPublicKey(),
        'endpoints' => $vars['user']->getActivityPubEndpoints(),
    ]);
    echo $person->toJson(JSON_PRETTY_PRINT);
} else {
    if ( isset($vars['object']) && $vars['object']?->isPublic()) {

        $owner = $vars['object']->getOwner();
        $ccTargets = [];
        if ($owner) {
            $ccTargets[] = $owner->getActivityPubFollowersURL();
        }

        $note = Type::create('Note', [
            '@context' => [
                'https://www.w3.org/ns/activitystreams',
            ],
            'id' => $vars['object']->getUUID(),
            'url' => ($vars['object']->getURL()),
            'attributedTo' => $vars['object']->getActivityPubActorID(),
            'to' => $vars['object']->getAddressedTo(),
            'cc' => $ccTargets,
            'published' => $vars['object']->getPublishedTime(),
            'content' => $vars['object']->getFormattedContent(),
            'tag' => $vars['object']->getHashTagObjects(),
        ]);
        if ($vars['object']->getUpdatedTime()) {
            $note->updated = $vars['object']->getUpdatedTime();
        }
        if ($vars['object']->getFormattedAttachments()) {
            $note->attachment = $vars['object']->getFormattedAttachments();
        }
        if ( 'image' === $vars['object']?->getActivityStreamsObjectType()) {
            $note->content = $vars['object']->getTitle();
        }
        if ($vars['object']->isReply() && $vars['object']->getReplyToURLs()) {
            $replyUrls = $vars['object']->getReplyToURLs();
            $note->inReplyTo = is_array($replyUrls) ? $replyUrls[0] : $replyUrls;
        }
        echo $note->toJson(JSON_UNESCAPED_SLASHES);
    }
}
