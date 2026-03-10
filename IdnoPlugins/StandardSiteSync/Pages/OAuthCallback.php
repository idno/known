<?php

namespace IdnoPlugins\StandardSiteSync\Pages;

use Idno\Common\Page;
use IdnoPlugins\StandardSiteSync\ATProtoClient;

/**
 * OAuth callback handler for AT Protocol authorization.
 * Route: /admin/standardsitesync/callback
 */
class OAuthCallback extends Page
{

    function getContent()
    {
        $this->adminGatekeeper();

        $code = $this->getInput('code');
        $state = $this->getInput('state');
        $error = $this->getInput('error');

        if (!empty($error)) {
            $errorDesc = $this->getInput('error_description');
            \Idno\Core\Idno::site()->session()->addErrorMessage(
                'Authentication denied: ' . ($errorDesc ?: $error)
            );
            $this->forward(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/standardsitesync/');
            return;
        }

        if (empty($code)) {
            \Idno\Core\Idno::site()->session()->addErrorMessage('No authorization code received.');
            $this->forward(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/standardsitesync/');
            return;
        }

        // Retrieve pending auth data
        $pendingAuth = $_SESSION['standardsitesync_pending_auth'] ?? null;
        unset($_SESSION['standardsitesync_pending_auth']);

        if (empty($pendingAuth)) {
            \Idno\Core\Idno::site()->session()->addErrorMessage('Authentication session expired. Please try again.');
            $this->forward(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/standardsitesync/');
            return;
        }

        // Verify state
        if ($state !== $pendingAuth['state']) {
            \Idno\Core\Idno::site()->session()->addErrorMessage('Invalid authentication state. Please try again.');
            $this->forward(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/standardsitesync/');
            return;
        }

        try {
            // Exchange code for tokens
            $session = ATProtoClient::completeOAuthFlow($code, $pendingAuth);

            // Save the session
            \Idno\Core\Idno::site()->config()->standardsitesync_session = $session;
            \Idno\Core\Idno::site()->config()->save();

            \Idno\Core\Idno::site()->session()->addMessage(
                'Successfully connected to your AT Protocol PDS (' . $session['did'] . ').'
            );
        } catch (\Exception $e) {
            \Idno\Core\Idno::site()->logging()->error('StandardSiteSync: OAuth callback failed: ' . $e->getMessage());
            \Idno\Core\Idno::site()->session()->addErrorMessage(
                'Authentication failed: ' . $e->getMessage()
            );
        }

        $this->forward(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/standardsitesync/');
    }
}
