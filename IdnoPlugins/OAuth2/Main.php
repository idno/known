<?php

namespace IdnoPlugins\OAuth2 {

    class Main extends \Idno\Common\Plugin {

	function registerPages() {
	    \Idno\Core\site()->addPageHandler('/oauth2/authorise/?', '\IdnoPlugins\OAuth2\Pages\Authorisation');
	    \Idno\Core\site()->addPageHandler('/oauth2/access_token/?', '\IdnoPlugins\OAuth2\Pages\Token');
	    \Idno\Core\site()->addPageHandler('/oauth2/connect/?', '\IdnoPlugins\OAuth2\Pages\Connect');

	    // Adding OAuth2 app page
	    \Idno\Core\site()->addPageHandler('/account/oauth2/?', '\IdnoPlugins\OAuth2\Pages\Account\Applications');
	    \Idno\Core\site()->template()->extendTemplate('account/menu/items', 'account/oauth2/menu');
	}

	function registerEventHooks() {

	    // Authenticate!
	    \Idno\Core\site()->addEventHook('user/auth/request', function(\Idno\Core\Event $event) {
		if ($user = \IdnoPlugins\OAuth2\Main::authenticate())
		    $event->setResponse($user);

	    }, 0);
	}

	public static function authenticate() {

	    // Have we been provided with an access token
	    if ($access_token = \Idno\Core\Input::getInput('access_token')) {

		 \Idno\Core\Idno::site()->session()->setIsAPIRequest(true);

		// Get token
		if ($token = Token::getOne(['access_token' => $access_token])) {

		    // Check expiry
		    if ($token->isValid()) {

			// Token still valid, get the owner
			$owner = $token->getOwner();
				
			if ($owner) {
			    
			    \Idno\Core\site()->session()->refreshSessionUser($owner); // Log user on, but avoid triggering hook and going into an infinite loop!
			    			    
			    // Save session scope
			    $_SESSION['oauth2_token'] = $token;
			    
			    // Double check scope
			    if ($owner->oauth2[$token->key]['scope'] != $token->scope) 
				throw new \Exception("Token scope doesn't match that which was previously granted!");
			    
			    return $owner;
			    
			} else {
			    \Idno\Core\site()->triggerEvent('login/failure', array('user' => $owner));
			    
			    throw new \Exception("Token user could not be retrieved.");
			}
		    } else {
			throw new \Exception("Access token $access_token has expired.");
		    }
		} else {
		    throw new \Exception("Access token $access_token does not match any stored token.", LOGLEVEL_ERROR);
		}
	    }
	}

    }

}
