<?php

    /**
     * Remote user representation
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Entities {

        class RemoteUser extends \Idno\Entities\User implements \JsonSerializable
        {

            public function save()
            {
                // TODO: use a remote API to save to external sources if we have permission to
                return false;
            }
	    
	    public function checkPassword($password) {
		return false; // Remote users can never log in
	    }

	    public function getURL() {
		
		// Remote users don't have a local profile, so we need to override the remote url
		if (!empty($this->url))
		    return $this->url;
		
		return $this->getUUID();
	    }
	    
	    /**
	     * Set this user's remote profile url.
	     * @param type $url
	     */
	    public function setUrl($url) {
		$this->url = $url;
	    }
        }

    }