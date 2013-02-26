<?php

    /**
     * User representation
     * 
     * @package idno
     * @subpackage core
     */

	namespace Idno\Entities {
	
	    class User extends \Idno\Common\Entity {
		
		function getFollowing() {
		    
		}
		
		function setHandle($handle) {
		    $handle = trim($handle);
		    $handle = strtolower($handle);
		    if (!empty($handle)) {
			$this->handle = $handle;
		    }
		}
		
		function getHandle() {
		    return $this->handle;
		}
		
		/**
		 * Array of access groups that this user can *read* entities
		 * from
		 * 
		 * @return array
		 */
		
		function getReadAccessGroups() {
		    $return = array('PUBLIC');
		    if ($groups = site()->db()->getObjects('Idno\\Entities\\AccessGroup',array('members.read' => $this->getUUID()),MAXINT,0)) {
			$return = array_merge($return, $groups);
		    }
		    return $return;
		}
		
		/**
		 * Array of access groups that this user can *write* entities
		 * to
		 * 
		 * @return type 
		 */
		
		function getWriteAccessGroups() {
		    $return = array('PUBLIC');
		    if ($groups = site()->db()->getObjects('Idno\\Entities\\AccessGroup',array('members.write' => $this->getUUID()),MAXINT,0)) {
			$return = array_merge($return, $groups);
		    }
		    return $return;
		}
		
		/**
		 * Array of access group IDs that this user can *read* entities
		 * from
		 * 
		 * @return array
		 */
		
		function getReadAccessGroupIDs() {
		    return array('PUBLIC');
		}
		
		/**
		 * Array of access group IDs that this user can *write* entities
		 * to
		 * 
		 * @return type 
		 */
		
		function getWriteAccessGroupIDs() {
		    return array('PUBLIC');
		}
		
	    }
	    
	}