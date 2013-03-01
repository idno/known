<?php

    /**
     * ActivityStreams representation
     * 
     * ActivityStreams are the dominant feed format in idno. Posts are created
     * for most actions.
     * 
     * Note that descriptions for Activity Streams post components are drawn
     * from the Activity Streams spec.
     * 
     * @see http://activitystrea.ms/specs/json/1.0/
     * @package idno
     * @subpackage core
     */

	namespace Idno\Entities {
	
	    class ActivityStreamPost extends \Idno\Common\Entity {
		
		/**
		 * Describes the entity that performed the activity.
		 * @param \Idno\Common\Entity $actor 
		 */
		function setActor(\Idno\Common\Entity $actor) {
		    $this->actor = $this->entityToActivityStreamsObject($actor);
		}
		
		/**
		 * Identifies the action that the activity describes.
		 * Verbs should be one of the established ActivityStreams verb
		 * types.
		 * 
		 * @param string $verb 
		 */
		function setVerb($verb) {
		    $this->verb = $verb;
		}
		
		/**
		 * Describes the primary object of the activity.
		 * @param \Idno\Common\Entity $object 
		 */
		function setObject(\Idno\Common\Entity $object) {
		    $this->object = $this->entityToActivityStreamsObject($object);
		}
		
		/**
		 * Describes the target of the activity. The precise meaning of 
		 * the activity's target is dependent on the activities verb, 
		 * but will often be the object the English preposition "to". 
		 * For instance, in the activity, "John saved a movie to his 
		 * wishlist", the target of the activity is "wishlist".
		 * 
		 * @param \Idno\Common\Entity $target 
		 */
		function setTarget(\Idno\Common\Entity $target) {
		    $this->target = $this->entityToActivityStreamsObject($target);
		}
		
		/**
		 * Converts Idno entities into ActivityStreams objects
		 * 
		 * @param \Idno\Common\Entity $entity
		 * @return array
		 */
		function entityToActivityStreamsObject(\Idno\Common\Entity $entity) {
		    
		    $object = array();
		    $owner = $entity->getOwnerID();
		    if (!empty($owner)) $object['author'] = $this->entityToActivityStreamsObject($entity->getOwner());
		    $object['displayName'] = $entity->getTitle();
		    $object['id'] = $entity->getUUID();
		    $object['objectType'] = $entity->getActivityStreamsObjectType();
		    $object['published'] = date('Y-m-d\TH:i:sP',$entity->created);
		    $object['updated'] = date('Y-m-d\TH:i:sP',$entity->updated);
		    $object['url'] = $entity->getURL();
		    return $object;
		    
		}
		
	    }
	    
	}