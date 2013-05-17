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

    use Idno\Entities\ActivityStreamPost;

    class Entity extends Component
    {

        // Store the entity's attributes
        private $attributes = array(
            'access' => 'PUBLIC' // All entites are public by default
        );

        /**
         * Overloading the entity property read function, so we
         * can simply check $entity->$foo for any non-empty value
         * of $foo for any property of this entity.
         */

        function __get($name)
        {
            if (isset($this->attributes[$name])) return $this->attributes[$name];
            return null;
        }

        /**
         * Overloading the entity property write function, so
         * we can simply set $entity->$foo = $bar for any
         * non-empty value of $foo for any property of this entity.
         */

        function __set($name, $value)
        {
            $this->attributes[$name] = $value;
        }

        /**
         * Overloading the entity property isset check, so that
         * isset($entity->property) and empty($entity->property)
         * work as expected.
         */

        function __isset($name)
        {
            if (!empty($this->attributes[$name])) return true;
            return false;
        }

        /**
         * Overloading the entity constructor, in order to set the owner
         * to be the currently logged-in user if appropriate
         */

        function __construct()
        {
            $this->setOwner(\Idno\Core\site()->session()->currentUser());
        }

        /**
         * Saves this entity - either creating a new entry, or
         * overwriting the existing one.
         */

        function save()
        {

            // Adding when this entity was created (if it's new) & updated

            if (empty($this->created)) {
                $this->created = time();
            }
            $this->updated = time();

            // Adding this entity's owner (if we don't know already)

            $owner_id = $this->getOwnerID();
            if (\Idno\Core\site()->session()->isLoggedIn() && empty($owner_id)) {
                $this->setOwner(\Idno\Core\site()->session()->currentUser());
            }

            // Save it to the database

            $event = new \Idno\Core\Event(array('object' => $this));
            $event->setResponse(true);
            if (\Idno\Core\site()->events()->dispatch('save', $event)->response()) {
                $result = \Idno\Core\site()->db->saveObject($this);
            } else {
                $result = false;
            }
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
         * Add this entity to the feed
         * @param string $verb Verb to use (default: post)
         * @param string $title Title to use. First variable is always subject; second is always title. Default: '%s posted %s'
         * @return bool
         */
        function addToFeed($verb = 'post', $title = '%s posted %s') {
            $activityStreamPost = new \Idno\Entities\ActivityStreamPost();
            $owner = $this->getOwner();
            $activityStreamPost->setOwner($owner);
            $activityStreamPost->setActor($owner);
            $activityStreamPost->setTitle(sprintf($title,$owner->getTitle(),$this->getTitle()));
            $activityStreamPost->setVerb('post');
            $activityStreamPost->setObject($this);
            return $activityStreamPost->save();
        }

        /**
         * Delete this entity
         * @todo complete this
         * @return bool
         */
        function delete() {
            $event = new \Idno\Core\Event(array('object' => $this));
            $event->setResponse(true);
            if (\Idno\Core\site()->events()->dispatch('delete', $event)->response()) {
                if ($entries = \Idno\Entities\ActivityStreamPost::getByObjectUUID($this->getUUID())) {
                    foreach($entries as $entry) {
                        $entry->delete();
                    }
                }
                return \Idno\Core\db()->deleteRecord($this->getID());
            }
            return false;
        }

        /**
         * This method is important: it'll be run by the API whenever an entity is updated.
         * It takes input from the world and saves it to the entity.
         *
         * @return true|false
         */
        function saveDataFromInput() {
            // Extend this
            return true;
        }

        /**
         * Return the creation date of this entity, relative to now.
         * @return string
         */
        function getRelativePublishDate() {
            $distance = time() - $this->created;
            if ($distance < 86400) {
                if ($distance < 60) {
                    return $distance . 's';
                } else if ($distance < 360) {
                    return round($distance / 60) . 'm';
                } else {
                    return round($distance / 60 / 60) . 'h';
                }
            } else {
                return date('M d Y',$this->created);
            }
        }

        /**
         * Return the user that owns this entity
         *
         * @return \Idno\Entities\User
         */

        function getOwner()
        {
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

        function getOwnerID()
        {
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

        function setOwner($owner)
        {
            if ($owner instanceof \Idno\Entities\User) {
                $this->owner = $owner->getUUID();
                return true;
            } else {
                $this->owner = $owner;
            }
            return false;
        }

        /**
         * Retrieves the access group that this entity belongs to
         * @param boolean $idOnly Should we return the ID only? (Default: false)
         * @return AccessGroup | string
         */

        function getAccess($idOnly = false)
        {
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

        function setAccess($access)
        {
            if (
                $access instanceof \Idno\Entities\AccessGroup ||
                ($access = \Idno\Core\site()->db()->getObject($access) && $access instanceof \Idno\Entities\AccessGroup)
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

        function getTitle()
        {
            if (!empty($this->title))
                return $this->title;
            return get_class($this) . ' ' . $this->_id;
        }

        /**
         * Set the short description for this entity
         * @param string $title
         */

        function setTitle($title)
        {
            $this->title = $title;
        }

        /**
         * Return the Universal Unique IDentifier for this object (which also
         * happens to be a URI for it).
         *
         * @return type
         */

        function getUUID()
        {
            if (!empty($this->uuid)) {
                return $this->uuid;
            }
            if (!empty($this->_id)) {
                return \Idno\Core\site()->config()->url . 'view/' . $this->_id;
            }
            return false;
        }

        /**
         * Returns the ID of this object
         * @return string
         */
        function getID() {
            return $this->_id;
        }

        /**
         * Return a website address to view this object (defaults to the UUID
         * of the object)
         *
         * @return string
         */

        function getURL()
        {
            $uuid = $this->getUUID();
            if (!empty($uuid)) {
                return $uuid;
            }
            return \Idno\Core\site()->config()->url . $this->getClassName() . '/edit';
        }

        /**
         * Return a URI endpoint to edit this object (defaults to a variation of
         * the UUID of the object)
         * @return string
         */
        function getEditURL() {
            return \Idno\Core\site()->config()->url . $this->getClassSelector() . '/edit/' . $this->getID();
        }

        /**
         * Return a URI endpoint to delete this object (defaults to a variation of
         * the UUID of the object)
         * @return string
         */
        function getDeleteURL() {
            return \Idno\Core\site()->config()->url . $this->getClassSelector() . '/delete/' . $this->getID();
        }

        /**
         * Retrieve the Activity Streams object type identifier for this entity.
         * By default, idno entities are objects of type "article".
         *
         * @return string
         */

        function getActivityStreamsObjectType()
        {
            return 'article';
        }

        /**
         * Can a specified user (either an explicitly specified user ID
         * or the currently logged-in user if this is left blank) edit
         * this entity?
         *
         * @param string $user_id
         * @return true|false
         */

        function canEdit($user_id = '')
        {
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

        function canRead($user_id = '')
        {
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
        function getCollection()
        {
            return 'entities';
        }

        /**
         * Populate the attributes of this object from an array
         *
         * @param array $array
         */
        function loadFromArray($array)
        {
            if (!empty($array) && is_array($array)) {
                foreach ($array as $key => $value) {
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

        function saveToArray()
        {
            $array = $this->attributes;
            $array['entity_subtype'] = get_class($this);
            return $array;
        }

        /**
         * Draws this entity using the generic template entity/EntityClass
         * (note that the namespace is stripped) and the current default template.
         *
         * @return string The rendered entity.
         */
        function draw() {
            $t = \Idno\Core\site()->template();
            return $t->__(array(
                'object' => $this
            ))->draw('entity/' . $this->getClassName());
        }

        /**
         * Draws the form to edit this entity using the generic template entity/EntityClass/edit
         * (note that the namespace is stripped) and the current default template.
         *
         * @return string The rendered entity.
         */
        function drawEdit() {
            $t = \Idno\Core\site()->template();
            return $t->__(array(
                'object' => $this
            ))->draw('entity/' . $this->getClassName() . '/edit');
        }

        /**
         * Simple method to get objects of this class in reverse
         * chronological order, using the database getObjects call.
         *
         * @param array $search List of filter terms (default: none)
         * @param array $fields List of fields to return (default: all)
         * @param int $limit Number of items to return (default: 10)
         * @param int $offset Number of items to skip (default: 0
         * @return array
         */

        static function get($search = array(), $fields = array(), $limit = 10, $offset = 0)
        {
            return \Idno\Core\site()->db()->getObjects(get_called_class(), $search, $fields, $limit, $offset);
        }

        /**
         * Simple method to get objects of ANY class in reverse
         * chronological order, using the database getObjects call.
         *
         * @param array $search List of filter terms (default: none)
         * @param array $fields List of fields to return (default: all)
         * @param int $limit Number of items to return (default: 10)
         * @param int $offset Number of items to skip (default: 0
         * @return array
         */
        static function getFromAll($search = array(), $fields = array(), $limit = 10, $offset = 0)
        {
            $result=  \Idno\Core\site()->db()->getObjects('', $search, $fields, $limit, $offset);
            return $result;
        }

        /**
         * Retrieve a single record of the calling class with certain
         * characteristics, using the database getObjects call.
         *
         * @param array $search List of filter terms (default: none)
         * @param array $fields List of fields to return (default: all)
         * @return Entity
         */

        static function getOne($search = array(), $fields = array())
        {
            if ($records = self::get($search, $fields, 1))
                foreach ($records as $record)
                    return $record;
        }

        /**
         * Retrieve a single record with certain characteristics, using
         * the database getObjects call.
         *
         * @param array $search List of filter terms (default: none)
         * @param array $fields List of fields to return (default: all)
         * @return Entity
         */

        static function getOneFromAll($search = array(), $fields = array())
        {
            if ($records = self::getFromAll($search, $fields, 1))
                foreach ($records as $record)
                    return $record;
        }

        /**
         * Retrieve a single record by its database ID
         * @param string $id
         * @return Entity
         */

        static function getByID($id)
        {
            return self::getOneFromAll(array('_id' => new \MongoId($id)));
        }

        /**
         * Retrieve a single record by its UUID
         * @param string $uuid
         * @return Entity
         */

        static function getByUUID($uuid)
        {
            return self::getOneFromAll(array('uuid' => $uuid));
        }

    }

}
	