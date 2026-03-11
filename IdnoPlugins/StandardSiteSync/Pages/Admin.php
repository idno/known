<?php

namespace IdnoPlugins\StandardSiteSync\Pages;

use Idno\Common\Page;

/**
 * Admin settings page for StandardSiteSync.
 * Only controls the site-wide enable/disable toggle.
 * Per-user PDS connection is managed in user account settings.
 * Route: /admin/standardsitesync
 */
class Admin extends Page
{
    public function getContent()
    {
        $this->adminGatekeeper();

        $t = \Idno\Core\Idno::site()->template();

        $syncEnabled = !empty(\Idno\Core\Idno::site()->config()->standardsitesync_enabled);

        $body = $t->__(
            [
                'title'        => 'Standard.site Sync',
                'sync_enabled' => $syncEnabled,
            ]
        )->draw('standardsitesync/admin/settings');

        $t->__(
            [
                'title' => 'Standard.site Sync',
                'body'  => $body,
            ]
        )->drawPage();
    }

    public function postContent()
    {
        $this->adminGatekeeper();

        $enabled = $this->getInput('sync_enabled') ? true : false;
        \Idno\Core\Idno::site()->config()->standardsitesync_enabled = $enabled;
        \Idno\Core\Idno::site()->config()->save();

        if ($enabled) {
            \Idno\Core\Idno::site()->session()->addMessage('Standard.site sync has been enabled for this site. Users can now connect their PDS accounts from their account settings.');
        } else {
            \Idno\Core\Idno::site()->session()->addMessage('Standard.site sync has been disabled.');
        }

        $this->forward(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/standardsitesync/');
    }
}
