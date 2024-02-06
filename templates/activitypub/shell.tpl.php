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

    if ( 'person' === $vars['user']?->getActivityStreamsObjectType()) {

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

        if ( 'note' === $vars['object']?->getActivityStreamsObjectType()) {
        }
        if ( 'article' === $vars['object']?->getActivityStreamsObjectType()) {
        }
    }
