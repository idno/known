<?php

namespace IdnoPlugins\OAuth2 {


    class Code extends \Idno\Common\Entity {
	
	function __construct() {
	    
	    parent::__construct();
	    
	    $this->code = hash('sha256', mt_rand() . microtime(true));
	    $this->expires = strtotime('now + 10 minutes');
	    
	    $this->setTitle($this->code); // better stub generation, not that it matters
	}

	function saveDataFromInput() {

	    if (empty($this->_id)) {
		$new = true;
	    } else {
		$new = false;
	    }

	    $this->setAccess('PRIVATE');
	    return $this->save();
	}

	function jsonSerialize() { // Code is only ever serialised as part of something else
	    return $this->code;
	}
	
	function __toString() {
	    return $this->code;
	}
    }

}