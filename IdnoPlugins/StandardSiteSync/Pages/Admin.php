<?php

namespace IdnoPlugins\StandardSiteSync\Pages;

use Idno\Common\Page;
use IdnoPlugins\StandardSiteSync\ATProtoClient;

/**
 * Admin settings page for StandardSiteSync.
 * Route: /admin/standardsitesync
 */
class Admin extends Page
{

    function getContent()
    {
        $this->adminGatekeeper();

        $t = \Idno\Core\Idno::site()->template();

        $session = \Idno\Core\Idno::site()->config()->standardsitesync_session ?? [];
        $connected = !empty($session['access_token']) && !empty($session['did']);
        $syncEnabled = !empty(\Idno\Core\Idno::site()->config()->standardsitesync_enabled);

        $body = $t->__(
            [
                'title'        => 'Standard.site Sync',
                'connected'    => $connected,
                'sync_enabled' => $syncEnabled,
                'did'          => $session['did'] ?? '',
                'pds'          => $session['pds'] ?? '',
            ]
        )->draw('standardsitesync/admin/settings');

        $t->__(
            [
                'title' => 'Standard.site Sync',
                'body'  => $body,
            ]
        )->drawPage();
    }

    function postContent()
    {
        $this->adminGatekeeper();

        $action = $this->getInput('action');

        if ($action === 'connect') {
            // Start OAuth flow
            $handle = trim($this->getInput('handle'));
            if (empty($handle)) {
                \Idno\Core\Idno::site()->session()->addErrorMessage('Please enter your AT Protocol handle.');
                $this->forward(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/standardsitesync/');
                return;
            }

            try {
                $authData = ATProtoClient::startOAuthFlow($handle);

                // Store pending auth data in session
                $_SESSION['standardsitesync_pending_auth'] = $authData;

                // Redirect to authorization server
                $this->forward($authData['url']);
            } catch (\Exception $e) {
                \Idno\Core\Idno::site()->logging()->error('StandardSiteSync: OAuth start failed: ' . $e->getMessage());
                \Idno\Core\Idno::site()->session()->addErrorMessage('Failed to start authentication: ' . $e->getMessage());
                $this->forward(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/standardsitesync/');
            }
        } elseif ($action === 'toggle_sync') {
            $enabled = $this->getInput('sync_enabled') ? true : false;
            \Idno\Core\Idno::site()->config()->standardsitesync_enabled = $enabled;
            \Idno\Core\Idno::site()->config()->save();

            if ($enabled) {
                // Ensure publication record exists on PDS
                $session = \Idno\Core\Idno::site()->config()->standardsitesync_session ?? [];
                if (!empty($session['access_token'])) {
                    try {
                        $client = new ATProtoClient($session);
                        $client->putPublication();
                        \Idno\Core\Idno::site()->config()->standardsitesync_session = $client->getSession();
                        \Idno\Core\Idno::site()->config()->save();
                        \Idno\Core\Idno::site()->session()->addMessage('Sync enabled and publication record created on your PDS.');
                    } catch (\Exception $e) {
                        \Idno\Core\Idno::site()->logging()->error('StandardSiteSync: Failed to create publication: ' . $e->getMessage());
                        \Idno\Core\Idno::site()->session()->addMessage('Sync enabled but publication record creation failed: ' . $e->getMessage());
                    }
                }
            } else {
                \Idno\Core\Idno::site()->session()->addMessage('Sync disabled.');
            }

            $this->forward(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/standardsitesync/');
        }
    }
}
