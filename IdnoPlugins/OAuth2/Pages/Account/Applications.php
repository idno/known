<?php

namespace IdnoPlugins\OAuth2\Pages\Account {

    class Applications extends \Idno\Common\Page {

	function getContent() {
	    $this->gatekeeper();

	    $apps = \IdnoPlugins\OAuth2\Application::get(['owner' => \Idno\Core\site()->session()->currentUserUUID()], array(), 99999, 0); // TODO: make this more complete / efficient

	    $t = \Idno\Core\site()->template();
	    $t->body = $t->__(array('applications' => $apps))->draw('account/oauth2');
	    $t->title = 'Manage OAuth2 Applications';
	    $t->drawPage();
	}

	function postContent() {

	    $this->gatekeeper();

	    $action = $this->getInput('action');

	    switch ($action) {
		case 'create' :
		    $app = \IdnoPlugins\OAuth2\Application::newApplication($this->getInput('name'));

		    if ($app->save())
			\Idno\Core\site()->session()->addMessage("New application " . $app->getTitle() . " created!");
		    else
			\Idno\Core\site()->session()->addErrorMessage("Problem creating new application...");
		    break;
		case 'delete' :
		    $uuid = $this->getInput('app_uuid');
		    if ($app = \IdnoPlugins\OAuth2\Application::getByUUID($uuid)) {
			if ($app->delete()) {
			    \Idno\Core\site()->session()->addMessage($app->getTitle() . " was removed.");
			}
		    }
		    break;
	    }

	    $this->forward(\Idno\Core\site()->config()->getURL() . 'account/oauth2/');
	}

    }

}