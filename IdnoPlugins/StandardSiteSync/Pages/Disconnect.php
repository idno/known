<?php

namespace IdnoPlugins\StandardSiteSync\Pages;

use Idno\Common\Page;

/**
 * Disconnect from AT Protocol PDS.
 * Route: /admin/standardsitesync/disconnect
 */
class Disconnect extends Page
{

    function postContent()
    {
        $this->adminGatekeeper();

        \Idno\Core\Idno::site()->config()->standardsitesync_session = [];
        \Idno\Core\Idno::site()->config()->standardsitesync_enabled = false;
        \Idno\Core\Idno::site()->config()->save();

        \Idno\Core\Idno::site()->session()->addMessage('Disconnected from AT Protocol PDS.');
        $this->forward(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/standardsitesync/');
    }
}
