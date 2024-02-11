<?php

    use ActivityPhp\Type;

    header('Content-type: application/ld+json; profile="https://www.w3.org/ns/activitystreams"');

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

        $person = Type::create('Person', [
            '@context' => [
                'https://www.w3.org/ns/activitystreams',
                'https://w3id.org/security/v1',
            ],
            'id' => $vars['user']->getActorID(),
            'url' => ($vars['user']->getAuthorURL()),
            'preferredUsername' => $vars['user']->getHandle(),
            'name' => $vars['user']->getAuthorName(),
            'summary' => $vars['user']->getDescription(),
            'icon' => $vars['user']->getIconObject(),
            'publicKey' => $vars['user']->getPublicKey(),
            // 'endpoints' => $vars['user']->getEndpoints(),
        ]);
        echo $person->toJson(JSON_PRETTY_PRINT);
    } else {
        if ( isset($vars['object']) && $vars['object']?->isPublic()) {
            $note = Type::create('Note', [
                '@context' => [
                    'https://www.w3.org/ns/activitystreams',
                ],
                'id' => $vars['object']->getUUID(),
                'url' => ($vars['object']->getURL()),
                'attributedTo' => $vars['object']->getActorID(),
                'to' => $vars['object']->getAddressedTo(),
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
            echo $note->toJson(JSON_UNESCAPED_SLASHES);            
        }
    }
