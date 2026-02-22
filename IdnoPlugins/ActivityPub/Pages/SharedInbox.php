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
        $this->setResponse(405);
        http_response_code(405);
        header('Allow: POST');
        echo json_encode(['error' => 'Method not allowed. POST to this endpoint.']);
        exit;
    }

    function postContent()
    {
        \Idno\Core\Idno::site()->session()->setApplyRecaptcha(false);

        $rawBody = file_get_contents('php://input');
        $headers = HTTPSignature::getRequestHeaders();
        $method = $_SERVER['REQUEST_METHOD'] ?? 'POST';
        $path = $_SERVER['REQUEST_URI'] ?? '/';

        // No target handle — shared inbox accepts for any user
        $result = ActivityHandler::handle($rawBody, $headers, $method, $path, null);

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
