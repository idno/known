<?php

namespace IdnoPlugins\IndiePub\Pages;

use Idno\Core\Idno;
use Idno\Common\Page;

class Revoke extends Page {

    function postContent()
    {
        $this->gatekeeper();

        $accturl = Idno::site()->config()->getDisplayURL().'account/indiepub/';
        $user = Idno::site()->session()->currentUser();
        $token = $this->getInput('token');

        if (!token) {
            $this->setResponse(400);
            Idno::site()->session()->addErrorMessage(\Idno\Core\Idno::site()->language()->_("Nothing to revoke."));
            return $this->forward($accturl);
        }

        if (!$user->indieauth_tokens || !isset($user->indieauth_tokens[$token])) {
            $this->setResponse(400);
            Idno::site()->session()->addErrorMessage(\Idno\Core\Idno::site()->language()->_("No IndiePub account with that token."));
            return $this->forward($accturl);
        }

        unset($user->indieauth_tokens[$token]);
        $user->save();

        return $this->forward($accturl);
    }

}