<?php

namespace IdnoPlugins\OAuth2\Pages\Account {

    class Applications extends \Idno\Common\Page
    {

        function getContent()
        {
            $this->gatekeeper();

            $apps = \IdnoPlugins\OAuth2\Application::get(['owner' => \Idno\Core\site()->session()->currentUserUUID()], array(), PHP_INT_MAX, 0); // TODO: make this more complete / efficient

            $t = \Idno\Core\site()->template();
            $t->body = $t->__(array('applications' => $apps))->draw('account/oauth2');
            $t->title = \Idno\Core\Idno::site()->language()->_('Manage OAuth2 Applications');
            $t->drawPage(true, 'settings-shell');
        }

        function postContent()
        {

            $this->gatekeeper();

            $action = $this->getInput('action');

            switch ($action) {
                case 'create' :
                    $app = \IdnoPlugins\OAuth2\Application::newApplication($this->getInput('name'));

                    if ($app->save()) {
                        \Idno\Core\site()->session()->addMessage(\Idno\Core\Idno::site()->language()->_("New application %s created!", [$app->getTitle()]));
                    } else {
                        \Idno\Core\site()->session()->addErrorMessage(\Idno\Core\Idno::site()->language()->_("Problem creating new application..."));
                    }
                    break;
                case 'delete' :
                    $uuid = $this->getInput('app_uuid');
                    if ($app = \IdnoPlugins\OAuth2\Application::getByUUID($uuid)) {
                        if ($app->delete()) {
                            \Idno\Core\site()->session()->addMessage(\Idno\Core\Idno::site()->language()->_("%s was removed.", [$app->getTitle()]));
                        }
                    }
                    break;
            }

            $this->forward(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'account/oauth2/');
        }

    }

}
