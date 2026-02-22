<?php

namespace IdnoPlugins\ActivityPub\Pages;

use IdnoPlugins\ActivityPub\ActivityHandler;
use IdnoPlugins\ActivityPub\HTTPSignature;

/**
 * Per-user ActivityPub inbox endpoint.
 * Route: /actor/{handle}/inbox
 */
class Inbox extends \Idno\Common\Page
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
        // Disable CSRF verification for incoming federation requests
        \Idno\Core\Idno::site()->session()->setApplyRecaptcha(false);

        $handle = $this->arguments[0] ?? '';

        $rawBody = file_get_contents('php://input');
        $headers = HTTPSignature::getRequestHeaders();
        $method = $_SERVER['REQUEST_METHOD'] ?? 'POST';
        $path = $_SERVER['REQUEST_URI'] ?? '/';

        \Idno\Core\Idno::site()->logging()->info(
            'ActivityPub Inbox: POST for handle=' . $handle .
            ' body_length=' . strlen($rawBody) .
            ' from=' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') .
            ' content_type=' . ($headers['content-type'] ?? 'none')
        );

        $result = ActivityHandler::handle($rawBody, $headers, $method, $path, $handle);

        \Idno\Core\Idno::site()->logging()->info(
            'ActivityPub Inbox: Response status=' . $result['status'] . ' for handle=' . $handle
        );

        $this->setResponse($result['status']);
        http_response_code($result['status']);
        header('Content-Type: application/json');
        echo $result['body'];
        exit;
    }

    /**
     * Override to disable CSRF token verification for federation endpoints.
     */
    function csrfGatekeeper()
    {
        return true;
    }
}
