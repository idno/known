<?php

namespace IdnoPlugins\IndiePub\Pages;

use Idno\Core\Idno;
use Idno\Common\Page;

class Add extends Page {

    function postContent()
    {
        $this->gatekeeper();

        $user = Idno::site()->session()->currentUser();
        $client_id = $this->getInput('client_id');
        $redirect_uri = $this->getInput('redirect_uri');
        $valid = true;

        if (empty($client_id)) {
            Idno::site()->session()->addErrorMessage(\Idno\Core\Idno::site()->language()->_("Client ID can't be blank."));
            $valid = false;
        }
        if (empty($redirect_uri)) {
            Idno::site()->session()->addErrorMessage(\Idno\Core\Idno::site()->language()->_("Redirect URI can't be blank."));
            $valid = false;
        }

        if (!$valid) return $this->forward(Idno::site()->config()->getDisplayURL().'account/indiepub/');

        $indieauth_tokens = $user->indieauth_tokens;
        if (empty($indieauth_tokens)) {
            $indieauth_tokens = array();
        }

        // Generate a new IndieAuth token
        $token = md5(rand(0, 99999) . time() . $user->getID() . $client_id . rand(0, 999999));

        $indieauth_tokens[$token] = array(
            'me'           => '',
            'redirect_uri' => $redirect_uri,
            'scope'        => 'create media',
            'client_id'    => $client_id,
            'issued_at'    => time(),
            'nonce'        => mt_rand(1000000, pow(2, 30))
        );

        $user->indieauth_tokens   = $indieauth_tokens;
        $user->save();

        \Idno\Core\Idno::site()->session()->refreshSessionUser($user);

        $this->forward(Idno::site()->config()->getDisplayURL().'account/indiepub/');
    }

}