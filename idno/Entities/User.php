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
		    return $this->getXAccessGroups('read');
		}
		
		/**
		 * Array of access groups that this user can *write* entities
		 * to
		 * 
		 * @return array
		 */
		
		function getWriteAccessGroups() {
		    return $this->getXAccessGroups('write');
		}
		
		/**
		 * Get an array of access groups that this user has arbitrary permissions for
		 * 
		 * @param string $permission The type of permission
		 * @return array
		 */
		function getXAccessGroups($permission) {
		    $return = array('PUBLIC');
		    if ($groups = site()->db()->getObjects('Idno\\Entities\\AccessGroup',array('members.' . $permission => $this->getUUID()),null,MAXINT,0)) {
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
		    return $this->getXAccessGroupIDs('read');
		}
		
		/**
		 * Array of access group IDs that this user can *write* entities
		 * to
		 * 
		 * @return type 
		 */
		
		function getWriteAccessGroupIDs() {
		    return $this->getXAccessGroupIDs('write');
		}
		
		/**
		 * Get an array of access group IDs that this user has an arbitrary permission for
		 * 
		 * @param string $permission Permission type
		 * @return array
		 */
		function getXAccessGroupIDs($permission) {
		    $return = array('PUBLIC');
		    if ($groups = site()->db()->getRecords(	array(	'uuid'=>true), 
								array(
									'entity_subtype' => 'Idno\\Entities\\AccessGroup', 
									'members.' . $permission => $this->getUUID()),
								MAXINT, 
								0)) {
			foreach($groups as $group) {
			    $return[] = $group->uuid;
			}
		    }
		    return $return;
		}
		
	    }
	    
	}