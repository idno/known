<?php

    /**
     * Base entity class
     * 
     * This is designed to be inherited by anything that needs to be an
     * object in the idno system
     * 
     * @package idno
     * @subpackage entities
     */

	namespace Idno\Entities {
	
	    class Entity {
		
		// Store the entity's attributes
		    private $attributes = array();
		    
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
		 * Saves this entity - either creating a new entry, or
		 * overwriting the existing one.
		 */
		    
		    function save() {
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
		    }
		
	    }
	    
	}