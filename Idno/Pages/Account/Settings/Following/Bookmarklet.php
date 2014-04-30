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
				$hcard[] = $item;
			}

			if (!count($hcard))
			    throw new \Exception("Sorry, could not find any users on that page, perhaps they need to mark up their profile in <a href=\"http://microformats.org/wiki/microformats-2\">Microformats</a>?"); // TODO: Add a manual way to add the user

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

	    if ($uuid = $this->getInput('uuid')) {

		if (
			// TODO: Do this better, perhaps support late bindings
			(!$new_user = \Idno\Entities\User::getByUUID($uuid)) &&  
			(!$new_user = \Idno\Entities\User::getByProfileURL($uuid)) &&
			(!$new_user = \Idno\Entities\RemoteUser::getByUUID($uuid)) &&  
			(!$new_user = \Idno\Entities\RemoteUser::getByProfileURL($uuid))
		) {
		    
		    // No user found, so create it if it's remote
		    if (!\Idno\Entities\User::isLocalUUID($uuid)) {
			error_log("Creating new remote user");
			
			$new_user = new \Idno\Entities\RemoteUser();

			// Populate with data
			$new_user->setTitle($this->getInput('name'));
			$new_user->setHandle($this->getInput('nickname'));
			$new_user->email = $this->getInput('email');
			$new_user->setUrl($uuid);
			
			if (!$new_user->save())
			    throw new \Exception ("There was a problem saving the new remote user.");
		    }
		} else
		    error_log("New user found as " . $new_user->uuid);

		if ($new_user) {
		    
		    error_log("Trying a follow");
		    
		    if ($user->addFollowing($new_user)) {
			
			error_log("User added to following");
			
			if ($user->save()) {
			    
			    error_log("Following saved");
			    
			    \Idno\Core\site()->session()->addMessage("You are now following " . $new_user->getTitle());
			}
		    } 
		} else
		    throw new \Exception('Sorry, that user doesn\'t exist!');
	    } else 
		throw new \Exception("No UUID, please try that again!");
	}

    }

}