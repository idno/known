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
    }

    function postContent()
    {
    }
}
