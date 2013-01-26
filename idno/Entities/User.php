<?php

    /**
     * User representation
     * 
     * @package idno
     * @subpackage core
     */

	namespace Idno\Entities {
	
	    class User extends \Idno\Common\Entity {
		
		/**
		 * Array of access groups that this user can *read* entities
		 * from
		 * 
		 * @return array
		 */
		
		function getReadAccessGroups() {
		    return array();
		}
		
		/**
		 * Array of access groups that this user can *write* entities
		 * to
		 * 
		 * @return type 
		 */
		
		function getWriteAccessGroups() {
		    return array();
		}
		
	    }
	    
	}