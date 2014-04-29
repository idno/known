<?php

/**
 * Bookmarklet endpoint
 */

namespace Idno\Pages\Account\Settings\Following {

    /**
     * Default class to serve the following settings
     */
    class Bookmarklet extends \Idno\Common\Page {

	function getContent() {
	    $this->gatekeeper();
	    $user = \Idno\Core\site()->session()->currentUser();

	    $u = $this->getInput('u');

	    if ($content = \Idno\Core\Webservice::get($u)['content']) {

		$parser = new \Mf2\Parser($content, $u);
		if ($return = $parser->parse()) {

		    if (isset($return['items'])) {

			$t = \Idno\Core\site()->template();
			$body = '';
			$hcard = [];
			
			foreach ($return['items'] as $item) {

			    // Find h-card
			    if (in_array('h-card', $item['type'])) 
				$hcard = $item['type'];
				
			}
			
			if (!count($hcard))
			    throw new \Exception("Sorry, could not find any users on that page!");
			
			foreach ($hcard as $card)
			    $body .= $t->__(['mf2' => $card])->draw('account/settings/following/mf2user');
			
			// List user
			$t->body = $body;
			$t->title = 'Found users';
			$t->drawPage();
		    }
		} else
		    throw new \Exception("Sorry, there was a problem parsing the page!");
	    } else
		throw new \Exception("Sorry, $u could not be retrieved!");

	    // forward back
	    $this->forward($_SERVER['HTTP_REFERER']);
	}

	function postContent() {
	    $this->gatekeeper();
	    $user = \Idno\Core\site()->session()->currentUser();


	    $uuid = $this->getInput('uuid');
	    if (!$new_user = \Idno\Entities\User::getByUUID($uuid)) {
		// Not a user, so create it if it's remote
		if (!\Idno\Entities\User::isLocalUUID($uuid)) {
		    $new_user = new \Idno\Entities\RemoteUser();

		    // TODO: Populate with data
		}
	    }

	    if ($new_user) {
		if ($user->addFollowing($new_user)) {
		    \Idno\Core\site()->session()->addMessage("User added!");
		}
	    } else
		throw new \Exception('Sorry, that user doesn\'t exist!');

	    // forward back
	    $this->forward($_SERVER['HTTP_REFERER']);
	}

    }

}