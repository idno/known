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
		
		/**
		 * Sets this user's username handle (and balks if someone's already using it)
		 * @param string $handle 
		 * @return true|false True or false depending on success
		 */
		
		function setHandle($handle) {
		    $handle = trim($handle);
		    $handle = strtolower($handle);
		    if (!empty($handle)) {
			if (!self::getByHandle($handle)) {
			    $this->handle = $handle;
			}
		    }
		    return false;
		}
		
		/**
		 * Retrieve's this user's handle
		 * @return string
		 */
		
		function getHandle() {
		    return $this->handle;
		}
		
		/**
		 * Does this user have everything he or she needs to be a fully-fledged
		 * idno member? This method checks to make sure the minimum number of
		 * fields are filled in.
		 * 
		 * @return true|false
		 */
		
		function isComplete() {
		    if (!empty($this->getHandle()) && !empty($this->getTitle())) return true;
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
		    if ($groups = \Idno\Core\site()->db()->getObjects('Idno\\Entities\\AccessGroup',array('members.' . $permission => $this->getUUID()),null,PHP_INT_MAX,0)) {
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
		    if ($groups = \Idno\Core\site()->db()->getRecords(	array(	'uuid'=>true), 
								array(
									'entity_subtype' => 'Idno\\Entities\\AccessGroup', 
									'members.' . $permission => $this->getUUID()),
								PHP_INT_MAX, 
								0)) {
			foreach($groups as $group) {
			    $return[] = $group->uuid;
			}
		    }
		    return $return;
		}
		
		/**
		 * Retrieves user by handle
		 * @param $handle
		 * @return User|false Depending on success
		 */
		static function getByHandle($handle) {
		    if ($result = \Idno\Core\site()->db()->getObjects('Idno\\Entities\\User', array('handle' => $handle), null, 1)) {
			foreach($result as $row) {
			    return $row;
			}
		    }
		    return false;
		}
		
	    }
	    
	}