<?php

namespace IdnoPlugins\ActivityPub\Pages;

use IdnoPlugins\ActivityPub\ActivityHandler;
use IdnoPlugins\ActivityPub\HTTPSignature;

/**
 * Shared ActivityPub inbox endpoint.
 * Route: /inbox
 * Receives activities addressed to any user on this instance.
 */
class SharedInbox extends \Idno\Common\Page
{

    function getContent()
    {
        $siteUrl = \Idno\Core\Idno::site()->config()->getDisplayURL();

        // Per spec, inbox MUST be an OrderedCollection.
        // We return an empty one since received activities are not publicly exposed.
        $collection = [
            '@context'     => 'https://www.w3.org/ns/activitystreams',
            'id'           => $siteUrl . 'inbox',
            'type'         => 'OrderedCollection',
            'totalItems'   => 0,
            'orderedItems' => [],
        ];

        header('Content-Type: application/activity+json');
        echo json_encode($collection, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        exit;
    }

    function postContent()
    {
        \Idno\Core\Idno::site()->session()->setApplyRecaptcha(false);

        $rawBody = file_get_contents('php://input');
        $headers = HTTPSignature::getRequestHeaders();
        $method = $_SERVER['REQUEST_METHOD'] ?? 'POST';
        $path = $_SERVER['REQUEST_URI'] ?? '/';

        \Idno\Core\Idno::site()->logging()->info(
            'ActivityPub SharedInbox: POST body_length=' . strlen($rawBody) .
            ' from=' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') .
            ' content_type=' . ($headers['content-type'] ?? 'none')
        );

        // No target handle — shared inbox accepts for any user
        $result = ActivityHandler::handle($rawBody, $headers, $method, $path, null);

        \Idno\Core\Idno::site()->logging()->info(
            'ActivityPub SharedInbox: Response status=' . $result['status']
        );

        $this->setResponse($result['status']);
        http_response_code($result['status']);
        header('Content-Type: application/json');
        echo $result['body'];
        exit;
    }

    function csrfGatekeeper()
    {
        return true;
    }
}
