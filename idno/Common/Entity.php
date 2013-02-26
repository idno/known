<?php

    /**
     * Base entity class
     * 
     * This is designed to be inherited by anything that needs to be an
     * object in the idno system
     * 
     * @package idno
     * @subpackage core
     */

	namespace Idno\Common {
	
	    class Entity {
		
		// Store the entity's attributes
		    private $attributes = array(
			'access'    =>	    'PUBLIC' // All entites are public by default
		    );
		    
		/**
		 * Overloading the entity property read function, so we
		 * can simply check $entity->$foo for any non-empty value
		 * of $foo for any property of this entity.
		 */
		    
		    function __get($name) {
			if (isset($this->attributes[$name])) return $this->attributes[$name];
			return null;
		    }
		    
		/**
		 * Overloading the entity property write function, so
		 * we can simply set $entity->$foo = $bar for any
		 * non-empty value of $foo for any property of this entity.
		 */
		    
		    function __set($name, $value) {
			$this->attributes[$name] = $value;
		    }
		    
		/**
		 * Overloading the entity property isset check, so that
		 * isset($entity->property) and empty($entity->property)
		 * work as expected.
		 */
		    
		    function __isset($name) {
			if (!empty($this->attributes[$name])) return true;
			return false;
		    }
		    
		/**
		 * Saves this entity - either creating a new entry, or
		 * overwriting the existing one.
		 */
		    
		    function save() {
			
			// Adding when this entity was created (if it's new) & updated
			
			if (empty($this->created)) {
			    $this->created = time();
			}
			$this->updated = time();
			
			// Adding this entity's owner (if we don't know already)
			
			if (\Idno\Core\site()->session()->isLoggedIn()) {
			    $this->setOwner(\Idno\Core\site()->session()->currentUser());
			}
			
			// Save it to the database
			
			$result = \Idno\Core\site()->db->saveObject($this);
			if (!empty($result)) {
			    if (empty($this->_id)) {
				$this->_id = $result;
				$this->uuid = $this->getUUID();
				\Idno\Core\site()->db->saveObject($this);
			    }
			    return $this->_id;
			} else {
			    return false;
			}
		    }
		    
		/**
		 * Return the user that owns this entity
		 * 
		 * @return User
		 */
		    
		    function getOwner() {
			if (!empty($this->owner)) {
			    return \Idno\Core\db()->getObject($this->owner);
			}
			return false;
		    }
		    
		/**
		 * Retrieve the UUID of the owner of this object (if one exists)
		 * 
		 * @return string | false
		 */
		    
		    function getOwnerID() {
			if (!empty($this->owner)) {
			    return $this->owner;
			}
			return false;
		    }
		    
		/**
		 * Set the owner of this entity to a particular user
		 * 
		 * @param User $owner 
		 * @return true|false
		 */
		    
		    function setOwner($owner) {
			if ($owner instanceof \Idno\Entities\User) {
			    $this->owner = $owner->getUUID();
			    return true;
			}
			return false;
		    }
		    
		/**
		 * Retrieves the access group that this entity belongs to
		 * @param boolean $idOnly Should we return the ID only? (Default: false)
		 * @return AccessGroup | string
		 */
		    
		    function getAccess($idOnly = false) {
			$access = $this->access;
			if (!$idOnly && $access != 'PUBLIC') {
			    $access = site()->db()->getObject($access);
			}
			return $access;
		    }
		    
		/**
		 * Set the access group of this object
		 * @param mixed $access The ID of the access group or an AccessGroup object
		 * return true|false
		 */
		    
		    function setAccess($access) {
			if (
				$access instanceof \Idno\Entities\AccessGroup || 
				($access = site()->db()->getObject($access) && $access instanceof \Idno\Entities\AccessGroup)
			    ) {
				$this->access = $access->getUUID();
				return true;
			    }
			return false;
		    }
		    
		/**
		 * Retrieve a short description for this entity
		 * @return string
		 */
		    
		    function getTitle() {
			if (!empty($this->title))
			    return $this->title;
			return get_class($this) . ' ' . $this->_id;
		    }
		    
		/**
		 * Set the short description for this entity
		 * @param string $title 
		 */
		    
		    function setTitle($title) {
			$this->title = $title;
		    }
		    
		/**
		 * Return the Universal Unique IDentifier for this object (which also
		 * happens to be a URI for it).
		 * 
		 * @return type 
		 */
		    
		    function getUUID() {
			if (!empty($this->uuid))
			    return $this->uuid;
			return \Idno\Core\site()->config()->url . 'view/' . $this->_id;
		    }
		    
		/**
		 * Can a specified user (either an explicitly specified user ID
		 * or the currently logged-in user if this is left blank) edit
		 * this entity?
		 * 
		 * @param string $user_id
		 * @return true|false
		 */
		    
		    function canEdit($user_id = '') {
			if (empty($user_id)) {
			    $user_id = \Idno\Core\site()->session()->currentUserUUID();
			}
			
			if ($this->getOwnerID() == $user_id) return true;
			
			return false;
		    }
		    
		/**
		 * Can a specified user (either an explicitly specified user ID
		 * or the currently logged-in user if this is left blank) view
		 * this entity?
		 * 
		 * @param string $user_id
		 * @return true|false
		 */
		    
		    function canRead($user_id = '') {
			if (empty($user_id)) {
			    $user_id = \Idno\Core\site()->session()->currentUserUUID();
			}
			$access = $this->getAccess();
			
			if ($access == 'PUBLIC') return true;
			if ($this->getOwnerID() == $user_id) return true;
			
			if ($access instanceof \Idno\Entities\AccessGroup) {
			    if ($access->isMember($user_id)) {
				return true;
			    }
			}
			
			return false;
		    }
		    
		/**
		 * Returns the database collection that this object should be 
		 * saved as part of
		 * 
		 * @return type 
		 */
		    function getCollection() {
			return 'entities';
		    }
		    
		/**
		 * Populate the attributes of this object from an array
		 * 
		 * @param array $array 
		 */
		    function loadFromArray($array) {
			if (!empty($array) && is_array($array)) {
			    foreach($array as $key => $value) {
				$this->attributes[$key] = $value;
			    }
			}
		    }
		    
		/**
		 * Store this object's attributes and class information as
		 * an array
		 * 
		 * @return array
		 */
		    
		    function saveToArray() {
			$array = $this->attributes;
			$array['entity_subtype'] = get_class($this);
			return $array;
		    }
		
	    }
	    
	}
	