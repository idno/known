<?php

namespace IdnoPlugins\StandardSiteSync\Pages;

use Idno\Common\Page;

/**
 * Serves the .well-known/site.standard.publication endpoint.
 * Returns the AT-URI of the publication record, confirming the link
 * between this domain and the publication on the PDS.
 * Route: /.well-known/site.standard.publication
 */
class WellKnownPublication extends Page
{

    function getContent()
    {
        $session = \Idno\Core\Idno::site()->config()->standardsitesync_session ?? [];

        if (empty($session['did'])) {
            $this->setResponse(404);
            echo 'No AT Protocol publication configured.';
            exit;
        }

        $atUri = 'at://' . $session['did'] . '/site.standard.publication/self';

        header('Content-Type: text/plain');
        header('Cache-Control: public, max-age=3600');
        echo $atUri;
        exit;
    }
}
