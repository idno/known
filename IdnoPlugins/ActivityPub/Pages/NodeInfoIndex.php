<?php

namespace IdnoPlugins\ActivityPub\Pages;

/**
 * NodeInfo discovery endpoint.
 * Route: /.well-known/nodeinfo
 * Returns links to the NodeInfo 2.0 document.
 */
class NodeInfoIndex extends \Idno\Common\Page
{

    function getContent()
    {
        $baseUrl = \Idno\Core\Idno::site()->config()->getDisplayURL();

        $response = [
            'links' => [
                [
                    'rel'  => 'http://nodeinfo.diaspora.software/ns/schema/2.0',
                    'href' => $baseUrl . 'nodeinfo/2.0',
                ],
            ],
        ];

        header('Content-Type: application/json');
        echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        exit;
    }

    function postContent()
    {
        $this->setResponse(405);
        http_response_code(405);
        header('Content-Type: application/json');
        header('Allow: GET');
        echo json_encode(['error' => 'Method not allowed']);
        exit;
    }
}
