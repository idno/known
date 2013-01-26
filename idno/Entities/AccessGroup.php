<?php

    /**
     * Access group representation
     * 
     * @package idno
     * @subpackage core
     */

	namespace Idno\Entities {
	
	    class AccessGroup extends \Idno\Common\Entity {
		
		function canRead($user_id = '') {
		    return true;
		}
		
	    }
	    
	}