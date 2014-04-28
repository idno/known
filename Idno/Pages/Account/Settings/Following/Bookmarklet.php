<?php

    /**
     * Bookmarklet endpoint
     */

    namespace Idno\Pages\Account\Settings\Following {

        /**
         * Default class to serve the following settings
         */
        class Bookmarklet extends \Idno\Common\Page
        {

            function getContent()
            {
                $this->gatekeeper(); 
		$user = \Idno\Core\site()->session()->currentUser();
		
		
		
		// Find users using MF2
		// List users, find uuid
	
		
		
		
		// forward back
                $this->forward($_SERVER['HTTP_REFERER']);
            }
	    
	    
	    function postContent() {
		$this->gatekeeper(); 
		$user = \Idno\Core\site()->session()->currentUser();
		
		
		$uuid = $this->getInput('uuid');
		if (!$new_user = \Idno\Entities\User::getByUUID($uuid)) {
		    // Not a user, so create it if it's remote
		    if (!\Idno\Entities\User::isLocalUUID($uuid))
		    {
			$new_user = new \Idno\Entities\RemoteUser();

			// TODO: Populate with data


		    }
		}

		if ($new_user) {
		    if ($user->addFollowing($new_user))
		    {
			\Idno\Core\site()->session()->addMessage("User added!");
		    }
		    
		} else
		    throw new \Exception('Sorry, that user doesn\'t exist!');
		
		// forward back
                $this->forward($_SERVER['HTTP_REFERER']);
	    }
        }

    }