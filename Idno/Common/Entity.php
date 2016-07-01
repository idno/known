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

        use Idno\Core\Idno;
        use Idno\Core\Webmention;
        use Idno\Entities\User;

        class Entity extends Component implements EntityInterface
        {

            // Which collection should this be stored in?
            public $collection = 'entities';
            static $retrieve_collection = 'entities';

            // Store the entity's attributes
            public $attributes = array(
                'access' => 'PUBLIC' // All entites are public by default
            );

            /**
             * Overloading the entity constructor, in order to set the owner
             * to be the currently logged-in user if appropriate
             */

            function __construct()
            {
                if (\Idno\Core\Idno::site()->session()) {
                    if ($user = \Idno\Core\Idno::site()->session()->currentUser()) {
                        $this->setOwner(\Idno\Core\Idno::site()->session()->currentUser());
                    }
                }
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
                if ($url = $this->getURL(true)) {
                    return $url;
                }
                if (!empty($this->_id)) {
                    return \Idno\Core\Idno::site()->config()->url . 'view/' . $this->_id;
                }

                return false;
            }

            /**
             * Count the number of objects of this class that we're allowed to see
             *
             * @param array $search List of filter terms (default: none)
             * @return int
             */
            static function count($search = array())
            {
                return \Idno\Core\Idno::site()->db()->countObjects(get_called_class(), $search);
            }

            /**
             * Count the number of objects of any class that we're allowed to see
             *
             * @param array $search
             * @return int
             */
            static function countFromAll($search = array())
            {
                return static::countFromX('', $search);
            }

            /**
             * Count the number of objects of any specified class(es) that we're allowed to see
             *
             * @param array|string $class Class(es) to search (blank for all)
             * @param array $search
             * @return int
             */
            static function countFromX($class, $search = array())
            {
                return \Idno\Core\Idno::site()->db()->countObjects($class, $search);
            }

            /**
             * Retrieve a single record of the calling class with certain
             * characteristics, using the database getObjects call.
             *
             * @param array $search List of filter terms (default: none)
             * @param array $fields List of fields to return (default: all)
             * @return Entity|false
             */

            static function getOne($search = array(), $fields = array())
            {
                if ($records = static::get($search, $fields, 1))
                    foreach ($records as $record)
                        return $record;

                return false;
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
                return \Idno\Core\Idno::site()->db()->getObjects(get_called_class(), $search, $fields, $limit, $offset, static::$retrieve_collection);
            }

            /**
             * Retrieve a single record by its database ID
             * @param string $id
             * @return Entity
             */

            static function getByID($id)
            {
                try {
                    return static::getOneFromAll(array('_id' => \Idno\Core\Idno::site()->db()->processID($id)));
                } catch (\Exception $e) {
                    return false; //\Idno\Core\Idno::site()->currentPage()->noContent();
                }
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
                if ($records = static::getFromAll($search, $fields, 1))
                    foreach ($records as $record)
                        return $record;

                return false;
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
                $result = static::getFromX('', $search, $fields, $limit, $offset);

                return $result;
            }

            /**
             * Simple method to get objects of a specified class or classes
             * in reverse chronological order, using the database getObjects call.
             *
             * @param string|array $class Class name(s) to check in (blank string, null or false for all)
             * @param array $search List of filter terms (default: none)
             * @param array $fields List of fields to return (default: all)
             * @param int $limit Number of items to return (default: 10)
             * @param int $offset Number of items to skip (default: 0)
             * @param array $readGroups Which ACL groups should we check? (default: everything the user can see)
             * @return array
             */
            static function getFromX($class, $search = array(), $fields = array(), $limit = 10, $offset = 0, $readGroups = [])
            {
                $result = \Idno\Core\Idno::site()->db()->getObjects($class, $search, $fields, $limit, $offset, static::$retrieve_collection, $readGroups);
                if (is_array($result)) $result = array_filter($result);

                return $result;
            }

            /**
             * Given a UUID of a remote entity, returns a native Entity object encapsulating it
             * @param $uuid
             * @return \Idno\Common\Entity
             */
            static function getRemote($uuid)
            {
                return false; // TODO: make this useful by parsing external mf2 and returning an appropriate entity object
            }

            /**
             * Retrieve a single record by its UUID
             * @param string $uuid
             * @return bool|Entity
             */

            static function getByUUID($uuid)
            {
                return static::getOneFromAll(array('uuid' => $uuid));
            }

            /**
             * Determines whether the given UUID refers to a local object (in which case it returns true)
             * or a remote object (in which case it turns false)
             * @param $uuid
             * @return bool
             */
            static function isLocalUUID($uuid)
            {
                // TODO: improve this heuristic
                // Parse the UUID
                if (($uuid_parse = parse_url($uuid)) && ($url_parse = parse_url(\Idno\Core\Idno::site()->config()->url))) {
                    if ($uuid_parse['host'] == $url_parse['host']) {
                        return true;
                    }
                }

                return false;
            }

            /**
             * Retrieves the category name of the content type associated with this class
             * @return string
             */
            function getContentTypeCategoryTitle()
            {
                if ($contentType = $this->getContentType()) {
                    return $contentType->getCategoryTitle();
                }

                return '';
            }

            /**
             * Retrieves the name of the content type associated with this class
             * @return string
             */
            function getContentTypeTitle()
            {
                if ($contentType = $this->getContentType()) {
                    return $contentType->getTitle();
                }

                return '';
            }

            /**
             * Retrieves the content type object associated with this class;
             * @return bool|ContentType
             */
            function getContentType()
            {
                return \Idno\Common\ContentType::getContentTypeObjectFromClass($this->getClass());
            }

            /**
             * Retrieves a URL-friendly name of the content type associated with this clas
             * @return string
             */
            function getContentTypeCategorySlug()
            {
                if ($contentType = $this->getContentType()) {
                    return $contentType->getCategoryTitleSlug();
                }

                return '';
            }

            /**
             * Overloading the entity property read function, so we
             * can simply check $entity->$foo for any non-empty value
             * of $foo for any property of this entity.
             */

            function &__get($name)
            {
                if (!isset($this->attributes[$name])) {
                    $this->attributes[$name] = null;
                }

                return $this->attributes[$name];

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
             * Set the short description for this entity
             * @param string $title
             */

            function setTitle($title)
            {
                $this->title = $title;
            }

            /**
             * Retrieve a short description for this entity
             * @return string
             */

            function getTitle()
            {
                if (!empty($this->title)) {
                    if (is_array($this->title)) {
                        $this->title = trim(implode(' ', $this->title));
                    }

                    return $this->title;
                }

                return get_class($this) . ' ' . $this->_id;
            }

            /**
             * Retrieve a title for this object suitable for notifications
             * @return string
             */
            function getNotificationTitle()
            {
                if ($title = $this->getTitle()) {
                    return $title;
                }
                if ($description = $this->getShortDescription()) {
                    return $description;
                }

                return '';
            }

            /**
             * Publishes this entity - either creating a new entry, or
             * overwriting the existing one. And then it will
             * syndicate the entity.
             */
            function publish()
            {
                if ($this->save()) {
                    $this->syndicate();
                    \Idno\Core\Idno::site()->triggerEvent('published', ['object' => $this]);

                    return true;
                }

                return false;
            }

            /**
             * Saves this entity - either creating a new entry, or
             * overwriting the existing one.
             *
             */

            function save()
            {

                // Adding this entity's owner (if we don't know already)

                $owner_id = $this->getOwnerID();
                if (\Idno\Core\Idno::site()->session()->isLoggedIn() && empty($owner_id)) {
                    $this->setOwner(\Idno\Core\Idno::site()->session()->currentUser());
                }

                // Automatically add a slug (if one isn't set and this is a new entity)

                if (!$this->getSlug() && empty($this->_id)) {
                    if (!($title = $this->getTitle())) {
                        if (!($title = $this->getDescription())) {
                            $title = md5(rand() . microtime(true));
                        }
                    }
                    \Idno\Core\Idno::site()->logging()->debug("Setting resilient slug");
                    $this->setSlugResilient($title);
                    \Idno\Core\Idno::site()->logging()->debug("Set resilient slug");
                } else {
                    \Idno\Core\Idno::site()->logging()->debug("Had slug: " . $this->getSlug());
                }

                // Force users to be public
                if ($this instanceof User) {
                    $this->access = 'PUBLIC';
                }

                // If this post has hashtags, save them separately for later retrieval
                if ($tags = $this->getTags()) {
                    $this->hashtags = $tags;
                }

                // Adding when this entity was created (if it's new) & updated
                if (empty($this->created)) {
                    $this->created = time();
                }
                $this->updated = time();

                // Save it to the database

                if (\Idno\Core\Idno::site()->triggerEvent('save', array('object' => $this))) { // dispatch('save', $event)->response()) {
                    $result = \Idno\Core\Idno::site()->db()->saveObject($this);
                } else {
                    $result = false;
                }
                if (!empty($result)) {
                    if (empty($this->_id)) {
                        $this->_id  = $result;
                        $this->uuid = $this->getUUID();
                        \Idno\Core\Idno::site()->db()->saveObject($this);
                        \Idno\Core\Idno::site()->triggerEvent('saved', ['object' => $this]);
                    } else {
                        \Idno\Core\Idno::site()->triggerEvent('updated', ['object' => $this]);
                    }

                    return $this->_id;
                } else {
                    return false;
                }
            }

            /**
             * Syndicate this content to third-party sites, if such plugins are installed
             */
            function syndicate()
            {
                if ($this->getActivityStreamsObjectType()) {
                    $event = new \Idno\Core\Event(array('object' => $this, 'object_type' => $this->getActivityStreamsObjectType()));
                    try {
                        \Idno\Core\Idno::site()->events()->dispatch('post/' . $this->getActivityStreamsObjectType(), $event);
                        \Idno\Core\Idno::site()->events()->dispatch('syndicate', $event);
                    } catch (\Exception $e) {
                        \Idno\Core\Idno::site()->session()->addErrorMessage("There was a problem syndicating.");
                        \Idno\Core\Idno::site()->logging()->error($e->getMessage());
                    }
                }
            }

            /**
             * Remove this content from third-party sites, if it was syndicated in the first place
             */
            function unsyndicate()
            {
                if ($this->getActivityStreamsObjectType()) {
                    $event = new \Idno\Core\Event(array('object' => $this, 'object_type' => $this->getActivityStreamsObjectType()));
                    \Idno\Core\Idno::site()->events()->dispatch('delete/' . $this->getActivityStreamsObjectType(), $event);
                    \Idno\Core\Idno::site()->events()->dispatch('unsyndicate', $event);
                }
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
             * Gets the URL slug for this entity, if it exists
             * @return bool|null
             */
            function getSlug()
            {
                if (!empty($this->slug)) {
                    return $this->slug;
                }

                return false;
            }

            /**
             * Sets the URL slug of this entity to the given non-empty string, modifying it
             * in the case where the slug is already taken, and returning the modified version
             * of the slug
             * @param string $slug
             * @param int $max_pieces The maximum number of words in the slug (default: 10)
             * @return bool|string
             */
            function setSlugResilient($slug, $max_pieces = 10)
            {
                // UUID max length is 255 chars; slug length <= 255 - (base URL + year + slash)
                $max_chars = 245 - strlen(\Idno\Core\Idno::site()->config()->getDisplayURL());
                if ($this->setSlug($slug, $max_pieces, $max_chars - 10)) {
                    return true;
                }
                // If we've got here, the slug exists, so we need to create a new version
                $slug_extension = 1;
                while (!($modified_slug = $this->setSlug($slug . '-' . $slug_extension, $max_pieces * 2, $max_chars))) {
                    $slug_extension++;
                }

                return $modified_slug;
            }

            /**
             * Processes the text involved in a slug
             * @param $slug
             * @param int $max_pieces The maximum number of words in the slug (default: 10)
             * @param int $max_chars The maximum number of characters in the slug (default: 255)
             * @return string
             */
            function prepareSlug($slug, $max_pieces = 10, $max_chars = 255)
            {
                $slug = trim($slug);
                if (is_callable('mb_strtolower')) {
                    $slug = mb_strtolower($slug, 'UTF-8');
                } else {
                    $slug = strtolower($slug);
                }
                $slug = strip_tags($slug);
                $slug = preg_replace('|https?://[a-z\.0-9]+|', '', $slug);
                $slug = preg_replace("/([^A-Za-z0-9%\p{L}\-\_ ])/u", '', $slug);
                $slug = preg_replace("/[ ]+/u", ' ', $slug);
                $slug = implode('-', array_slice(explode(' ', $slug), 0, $max_pieces));

                // trim the source string until the encoded string is <= max_chars
                for ($chars = $max_chars; $chars >= 0; $chars--) {
                    $truncated = mb_substr($slug, 0, $chars, 'UTF-8');
                    $encoded   = rawurlencode(mb_substr($slug, 0, $chars, 'UTF-8'));
                    if (strlen($encoded) <= $max_chars) {
                        $slug = $encoded;
                        break;
                    }
                }

                while (substr($slug, -1) == '-') {
                    $slug = substr($slug, 0, strlen($slug) - 1);
                }
                if (empty($slug)) {
                    $slug = 'untitled';
                }
                if (is_callable('mb_convert_encoding')) {
                    $slug = mb_convert_encoding($slug, 'UTF-8', 'UTF-8');
                }

                return $slug;
            }

            /**
             * Sets the URL slug of this entity to the given non-empty string, returning
             * the sanitized slug on success
             * @param string $slug
             * @param int $max_pieces The maximum number of words in the slug (default: 10)
             * @param int $max_chars The maximum number of characters in the slug (default: 255)
             * @return bool
             */
            function setSlug($slug, $max_pieces = 10, $max_chars = 255)
            {

                $plugin_slug = \Idno\Core\Idno::site()->triggerEvent('entity/slug', array('object' => $this));
                if (!empty($plugin_slug) && $plugin_slug !== true) {
                    return $plugin_slug;
                }
                $slug = $this->prepareSlug($slug, $max_pieces, $max_chars);
                if (empty($slug)) {
                    return false;
                }
                if ($entity = \Idno\Common\Entity::getBySlug($slug)) {
                    if ($entity->getUUID() != $this->getUUID()) {
                        return false;
                    }
                }
                $this->slug = $slug;

                return $slug;

            }

            /**
             * Retrieve a single record by its URL slug
             * @param $slug
             * @return bool|Entity
             */
            static function getBySlug($slug)
            {
                if (empty($slug)) {
                    return false;
                }

                return static::getOneFromAll(array('slug' => $slug));
            }

            /**
             * Retrieve the Activity Streams object type identifier for this entity.
             * By default, idno entities are objects of type "article".
             *
             * @return string
             */

            function getActivityStreamsObjectType()
            {
                return 'entity';
            }

            /**
             * Attaches a file reference to this entity
             * @param \MongoGridFSFile $file_wrapper
             */
            function attachFile($file_wrapper)
            {
                $file = $file_wrapper->file;
                if (empty($this->attachments) || !is_array($this->attachments)) {
                    $this->attachments = array();
                }
                $attachments       = $this->attachments;
                $attachments[]     = array('_id' => $file['_id'], 'url' => \Idno\Core\Idno::site()->config()->url . 'file/' . $file['_id'] . '/' . urlencode($file['filename']), 'mime-type' => $file['mime_type'], 'length' => $file['length']);
                $this->attachments = $attachments;
            }

            /**
             * Delete any files associated with this entity
             */
            function deleteAttachments()
            {
                if ($attachments = $this->getAttachments()) {
                    foreach ($attachments as $attachment) {
                        if ($file = \Idno\Entities\File::getByID($attachment['_id'])) {
                            $file->delete();
                        }
                    }

                    $this->attachments = [];
                }
            }

            /**
             * Returns an array of attachments to this entity.
             * @return array
             */
            function getAttachments()
            {
                if (!empty($this->attachments)) {
                    if (!empty(\Idno\Core\Idno::site()->config()->attachment_base_host)) {
                        $attachments = $this->attachments;
                        foreach ($this->attachments as $key => $value) {
                            if (!empty($value['url'])) {
                                $host         = parse_url($value['url'], PHP_URL_HOST);
                                $value['url'] = str_replace($host, \Idno\Core\Idno::site()->config()->attachment_base_host, $value['url']);
                                if (empty($value['filename'])) {
                                    $value['filename'] = basename($value['url']);
                                }
                                if (empty($value['mime_type'])) {
                                    $value['mime_type'] = 'application/octet-stream';
                                }
                                $attachments[$key] = $value;
                            }
                        }
                        $this->attachments = $attachments;
                    }

                    return $this->attachments;
                } else {
                    return array();
                }
            }

            /**
             * Delete this entity
             * @todo complete this
             * @return bool
             */
            function delete()
            {
                $event = new \Idno\Core\Event(array('object' => $this));
                $event->setResponse(true);
                if (\Idno\Core\Idno::site()->triggerEvent('delete', array('object' => $this))) {
                    $this->unsyndicate();

                    if ($return = \Idno\Core\db()->deleteRecord($this->getID(), $this->collection)) {
                        $this->deleteData();
                        \Idno\Core\Idno::site()->triggerEvent('deleted', array('object' => $this));

                        return $return;
                    }

                }

                return false;
            }

            /**
             * Returns the ID of this object
             * @return string
             */
            function getID()
            {
                return $this->_id;
            }

            /**
             * Retrieves an icon for this entity
             * @return mixed|string
             */
            function getIcon()
            {
                if ($user = $this->getOwner()) {
                    return $user->getIcon();
                }
                if ($page = \Idno\Core\Idno::site()->currentPage()) {
                    return $page->getIcon();
                }

                return \Idno\Core\Idno::site()->config()->getDisplayURL() . 'gfx/logos/logo_k.png';
            }

            /**
             * Is called after an entity is deleted but before the delete process finishes
             * @return bool
             */
            function deleteData()
            {
                return true;
            }

            /**
             * This method is important: it'll be run by the API whenever an entity is updated.
             * It takes input from the world and saves it to the entity.
             *
             * @return true|false
             */
            function saveDataFromInput()
            {
                // Extend this
                return true;
            }

            /**
             * Set the access group of this object
             * @param mixed $access The ID of the access group or an AccessGroup object
             * return true|false
             */

            function setAccess($access = -1)
            {
                if (empty($access)) {
                    $access = 'PUBLIC';
                }
                if ($access == 'PUBLIC') {
                    $this->access = 'PUBLIC';
                }
                if ($access == 'SITE') {
                    $this->access = 'SITE';
                }
                if ($this instanceof User) {
                    $access = 'PUBLIC';
                }
                $this->access = $access;

                return true;
            }

            /**
             * Retrieves a version of this entity's title suitable for using in URLs
             * @return string
             */
            function getPrettyURLTitle()
            {
                $clean = preg_replace("/[^a-zA-Z0-9\/_| -]/u", '', $this->getTitle());
                $clean = strtolower(trim($clean, '_'));
                $clean = preg_replace("/[\/_| -]+/u", '-', $clean);

                return urlencode($clean);
            }

            /**
             * Get the name of this entity type
             * @return string
             */
            function getEntityTypeName()
            {
                return substr(get_class($this), strrpos(get_class($this), '\\') + 1);
            }

            /**
             * Return an array of hashtags (if any) present in this entity's description.
             * @return array
             */
            function getTags()
            {
                if ($descr = $this->getDescription()) {
                    if (!empty($this->tags)) {
                        $descr .= ' ' . $this->tags;
                    }
                    if (preg_match_all('/(?<!=)(?<!["\'])(\#[A-Za-z0-9\_]+)/iu', $descr, $matches)) {
                        if (!empty($matches[0])) {
                            return $matches[0];
                        }
                    }
                }

                return array();
            }

            /**
             * Retrieve a text description of this entity
             * @return string
             */

            function getDescription()
            {
                if (!empty($this->description)) {
                    if (is_array($this->description)) {
                        $this->description = implode(' ', $this->description);
                    }

                    return $this->description;
                }

                return '';
            }

            /**
             * Retrieves this object's author name
             * @return bool|string
             */
            function getAuthorName()
            {
                if ($owner = $this->getOwner()) {
                    return $owner->getTitle();
                }

                return false;
            }

            /**
             * Retrieves this object's author URL
             * @return bool|string
             */
            function getAuthorURL()
            {
                if ($owner = $this->getOwner()) {
                    return $owner->getURL();
                }

                return false;
            }

            /**
             * Retrieves the rendered HTML of this body
             * @return string
             */
            function getBody()
            {
                if (!empty($this->body)) {
                    return $this->body;
                }

                return '';
            }

            /**
             * Get the URIs of all images in this entity's body HTML
             * @return array
             */
            function getImageSourcesFromBody($total = 0)
            {
                $src = array();
                if ($body = $this->getBody()) {
                    $doc = new \DOMDocument();
                    $doc->loadHTML($body);
                    if ($images = $doc->getElementsByTagName('img')) {
                        foreach ($images as $image) {
                            if ($source = $image->getAttribute('src')) {
                                $src[] = $source;
                                if ($total > 0 && sizeof($src) >= $total) {
                                    return $src;
                                }
                            }
                        }
                    }
                }

                return $src;
            }

            /**
             * Gets the URI of the first image in this entity's body HTML
             * @return bool
             */
            function getFirstImageSourceFromBody()
            {
                if ($src = $this->getImageSourcesFromBody()) {
                    return $src[0];
                }

                return false;
            }

            /**
             * Retrieves paragraphs from the body, optionally limiting the total number to $total
             * @param int $total
             * @return array
             */
            function getParagraphsFromBody($total = 0)
            {
                $src = array();
                if ($body = $this->getBody()) {
                    $doc = new \DOMDocument();
                    $doc->loadHTML($body);
                    if ($paras = $doc->getElementsByTagName('p')) {
                        foreach ($paras as $para) {
                            $src[] = $doc->saveHTML($para);
                            if ($total > 0 && sizeof($src) >= $total) {
                                return $src;
                            }
                        }
                    }
                }

                return $src;
            }

            /**
             * Get the time it would take to read this entity's body, in seconds.
             * @return int
             */
            function getReadingTimeInSeconds()
            {
                if ($body = $this->getBody()) {
                    $body  = strip_tags($body);
                    $words = str_word_count($body);

                    return (int)ceil(($words / 200) * 60);
                }

                return 0;
            }

            /**
             * Get the time it would take to read this entity's body, in minutes.
             * @return int
             */
            function getReadingTimeInMinutes()
            {
                return (int)ceil($this->getReadingTimeInSeconds() / 60);
            }

            /**
             * Sets the POSSE link for this entity to a particular service
             * @param string $service The name of the service
             * @param string $url The URL of the post
             * @param string $identifier A human-readable account identifier
             * @param string $item_id A Known-readable item identifier
             * @param string $account_id A Known-readable account identifier
             * @param array $other_properties (optional) additional properties to store with the link
             * @return bool
             */
            function setPosseLink($service, $url, $identifier = '', $item_id = '', $account_id = '', $other_properties = array())
            {
                if (!empty($service) && !empty($url)) {
                    $posse = $this->posse;
                    if (empty($identifier)) {
                        $identifier = $service;
                    }
                    $posse[$service][] = array_merge($other_properties, array(
                        'url'        => $url,
                        'identifier' => $identifier,
                        'item_id'    => $item_id,
                        'account_id' => $account_id
                    ));
                    $this->posse       = $posse;

                    return true;
                }

                return false;
            }

            /**
             * Retrieves an array of form ['service_name' => 'url'] that represents copies of this entity
             * on third-party services
             * @return array
             */
            function getPosseLinks()
            {
                if (!empty($this->posse)) {
                    return $this->posse;
                }

                return array();
            }

            /**
             * Retrieve a citation that references this entity
             * @return string
             */
            function getCitation()
            {
                $host     = parse_url(\Idno\Core\Idno::site()->config()->getURL(), PHP_URL_HOST);
                $shorturl = $this->getShortURL(false);

                return '(' . $host . ' s/' . $shorturl . ')';
            }

            /**
             * Retrieve a short URL that references this entity
             * @param bool $complete
             * @return null|string
             */
            function getShortURL($complete = true, $url_schema = true)
            {
                if (empty($this->shorturl)) {
                    $this->setShortURL();
                }
                if ($complete) {
                    if ($url_schema) {
                        $host = \Idno\Core\Idno::site()->config()->url;
                    } else {
                        $host = \Idno\Core\Idno::site()->config()->host . '/';
                    }

                    return $host . 's/' . $this->shorturl;
                }

                return $this->shorturl;
            }

            /**
             * Sets the short URL for this entity.
             * @return string
             */
            function setShortURL()
            {
                function shorten($id, $alphabet = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
                {
                    $base  = strlen($alphabet);
                    $short = '';
                    while ($id) {
                        $id    = ($id - ($r = $id % $base)) / $base;
                        $short = $alphabet{$r} . $short;
                    };

                    return $short;
                }

                $seed = rand(0, 99999999);
                $code = shorten($seed);
                while ($entity = static::getByShortURL($code)) {
                    $code = shorten(rand(0, 99999999));
                }
                $this->shorturl = $code;
                $this->save();

                return $this->shorturl;
            }

            /**
             * Retrieve a single record by its short URL
             * @param $url
             * @return bool|Entity
             */
            static function getByShortURL($url)
            {
                $url = str_replace(\Idno\Core\Idno::site()->config()->url . 's/', '', $url);
                if (empty($url)) {
                    return false;
                }

                return static::getOneFromAll(array('shorturl' => $url));
            }

            /**
             * Retrieve a short description of this page suitable for including in page metatags
             * @param $words Number of words to limit to, if we're generating a short description on the fly (default: 25)
             * @return string
             */

            function getShortDescription($words = 25)
            {
                if (!empty($this->short_description)) {
                    return $this->short_description;
                }

                $description = strip_tags($this->getDescription());
                $description = implode(' ', array_slice(explode(' ', $description), 0, $words));

                return $description;
            }

            /**
             * Return a URI endpoint to edit this object (defaults to a variation of
             * the UUID of the object)
             * @return string
             */
            function getEditURL()
            {
                return \Idno\Core\Idno::site()->config()->getDisplayURL() . $this->getClassSelector() . '/edit/' . $this->getID();
            }

            /**
             * Return a URI endpoint to delete this object (defaults to a variation of
             * the UUID of the object)
             * @return string
             */
            function getDeleteURL()
            {
                return \Idno\Core\Idno::site()->config()->getDisplayURL() . $this->getClassSelector() . '/delete/' . $this->getID();
            }

            /**
             * Retrieve the Microformats 2 root-level object type for this entity.
             * By default, this is 'h-entry'.
             *
             * @return string
             */
            function getMicroformats2ObjectType()
            {
                return 'h-entry';
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

                if (!\Idno\Core\Idno::site()->session()->isLoggedOn()) return false;
                if (!\Idno\Core\Idno::site()->canWrite()) return false;

                if (empty($user_id)) {
                    $user_id = \Idno\Core\Idno::site()->session()->currentUserUUID();
                }

                if ($user_id = \Idno\Core\Idno::site()->session()->currentUserUUID()) {
                    $user = \Idno\Core\Idno::site()->session()->currentUser();
                } else {
                    $user = User::getByUUID($user_id);
                }

                if ($user->isAdmin()) {
                    return true;
                }

                if ($this->getOwnerID() == $user_id) return true;

                return \Idno\Core\Idno::site()->triggerEvent('canEdit', array('object' => $this, 'user_id' => $user_id), false);

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
                    $user_id = \Idno\Core\Idno::site()->session()->currentUserUUID();
                }
                $access = $this->getAccess();

                if ($access == 'PUBLIC') return true;
                if ($access == 'SITE' && \Idno\Core\Idno::site()->session()->isLoggedIn()) return true;
                if ($this->getOwnerID() == $user_id) return true;

                if ($access instanceof \Idno\Entities\AccessGroup) {
                    if ($access->isMember($user_id)) {
                        return \Idno\Core\Idno::site()->triggerEvent('canRead', array('object' => $this, 'user_id' => $user_id, 'access_group' => $access));
                    }
                }

                return \Idno\Core\Idno::site()->triggerEvent('canRead', array('object' => $this, 'user_id' => $user_id), false);
            }

            /**
             * Retrieves the access group that this entity belongs to
             * @param boolean $idOnly Should we return the ID only? (Default: false)
             * @return AccessGroup | string
             */

            function getAccess($idOnly = false)
            {
                $access = $this->access;
                if (!$idOnly && $access != 'PUBLIC' && $access != 'SITE') {
                    $access = \Idno\Core\Idno::site()->db()->getObject($access);
                }

                return $access;
            }

            /**
             * Is this entity public?
             * @return bool
             */
            function isPublic()
            {
                $access = $this->getAccess();
                if ($access == 'PUBLIC') {
                    return true;
                }

                return false;
            }

            /**
             * Is this entity a reply to another entity?
             * @return bool
             */
            function isReply()
            {
                if (!empty($this->inreplyto)) {
                    return true;
                }

                return false;
            }

            /**
             * Get the URL of the object this entity is in reply to
             * @return array|bool
             */
            function getReplyToURLs()
            {
                if (!empty($this->inreplyto)) {
                    if (!is_array($this->inreplyto)) {
                        $this->inreplyto = [$this->inreplyto];
                    }

                    return $this->inreplyto;
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
                return $this->collection;
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
                $array                   = $this->attributes;
                $array['entity_subtype'] = get_class($this);

                return $array;
            }

            /**
             * Draws this entity using the generic template entity/EntityClass
             * (note that the namespace is stripped) and the current default template.
             * If entity/EntityClass doesn't exist, the template entity/template
             * is tried as a fallback.
             *
             * @param $feed_view If set to true, draws a version of the entity suitable for including in a feed, eg
             *                   RSS (false by default)
             *
             * @return string The rendered entity.
             */
            function draw($feed_view = false)
            {
                $t = \Idno\Core\Idno::site()->template();

                if ($this instanceof User) {
                    $params = ['user' => $this, 'feed_view' => $feed_view];
                } else {
                    $params = ['object' => $this, 'feed_view' => $feed_view];
                }

                $return = $t->__($params)->draw('entity/' . $this->getFullClassName(true), false);

                if ($return === false) {
                    $return = $t->__($params)->draw('entity/' . $this->getClassName(), false);
                    if ($return === false) {
                        $return = $t->__($params)->draw('entity/default');
                    }
                }

                return $return;
            }

            /**
             * Draws the form to edit this entity using the generic template entity/EntityClass/edit
             * (note that the namespace is stripped) and the current default template.
             *
             * @return string The rendered entity.
             */
            function drawEdit()
            {
                $t = \Idno\Core\Idno::site()->template();

                $return = $t->__(array(
                    'object' => $this
                ))->draw('entity/' . $this->getFullClassName(true) . '/edit');

                if ($return === false) {
                    $return = $t->__(array(
                        'object' => $this
                    ))->draw('entity/' . $this->getClassName() . '/edit');
                }

                return $return;
            }

            /**
             * Serialize this entity
             * @return array|mixed
             */
            public function jsonSerialize()
            {
                $object           = array(
                    'id'          => $this->getUUID(),
                    'content'     => strip_tags($this->getDescription()),
                    'formattedContent'
                                  => \Idno\Core\Idno::site()->template()->autop($this->getDescription()),
                    'displayName' => $this->getTitle(),
                    'objectType'  => $this->getActivityStreamsObjectType(),
                    'published'   => date(\DateTime::RFC3339, $this->created),
                    'url'         => $this->getURL()
                );
                $extra_properties = [
                    'lat'       => 'latitude',
                    'long'      => 'longitude',
                    'placename' => 'placeName',
                    'address'   => 'address',
                    'tags'      => 'tags',
                    'username'  => 'username'
                ];

                foreach ($extra_properties as $property => $parameter_name) {
                    if (!empty($this->$property)) {
                        $object[$parameter_name] = $this->$property;
                    }
                }

                if (isset($this->posse)) {
                    $object['syndication'] = $this->posse;
                }

                if ($owner = $this->getOwner()) {
                    if ($owner != $this) {
                        $object['actor'] = $owner;
                    }
                }

                if ($attachments = $this->getAttachments()) {
                    foreach ($attachments as $attachment) {
                        if (empty($attachment['mime-type'])) {
                            $attachment['mime-type'] = 'application/octet-stream';
                        }
                        if (empty($attachment['length'])) {
                            $attachment['length'] = 0;
                        }
                        $object['attachments'][] = [
                            'url'       => preg_replace('/^(https?:\/\/\/)/u', \Idno\Core\Idno::site()->config()->url, $attachment['url']),
                            'mime-type' => $attachment['mime-type'],
                            'length'    => $attachment['length']
                        ];
                    }
                }

                if (!empty($this->annotations)) {
                    $object['annotations'] = $this->annotations;
                }

                return $object;
            }

            /**
             * Get the routing regular expression for entities based
             * on the configured permalink structure. By convention
             * the route must contain one matching group that matches
             * either the post ID or the post slug.
             * @return string
             */
            static function getPermalinkRoute()
            {
                $parts  = explode('/', \Idno\Core\Idno::site()->config()->getPermalinkStructure());
                $result = implode('/', array_map(function ($part) {
                    switch ($part) {
                        case ':id':
                            return '([A-Za-z0-9]+)';
                        case ':slug':
                            return '([\%A-Za-z0-9\-\_]+)';
                        case ':year':
                            return '[0-9]{4}';
                        case ':month':
                            return '[0-9]{2}';
                        case ':day':
                            return '[0-9]{2}';
                        default:
                            return $part;
                    }
                }, $parts));

                return $result;
            }

            /**
             * Return a website address to view this object (defaults to the UUID
             * of the object)
             *
             * @param $new If set to true, doesn't attempt to generate a URL from a presaved uuid
             * @return string
             */

            function getURL()
            {

                // If we have a URL override, use it
                if (!empty($this->url)) {
                    return $this->url;
                }

                if (!empty($this->canonical)) {
                    return $this->canonical;
                }

                // build the permalink based on the configured structure
                $parts = explode('/', \Idno\Core\Idno::site()->config()->getPermalinkStructure());

                if (
                    (!in_array(':slug', $parts) || $this->getSlug()) &&
                    (!in_array(':id', $parts) || $this->getID())
                ) {
                    $path = implode('/', array_map(function ($part) {
                        switch ($part) {
                            case ':id':
                                return $this->getID();
                            case ':slug':
                                return $this->getSlug();
                            case ':year':
                                return strftime('%Y', $this->created);
                            case ':month':
                                return strftime('%m', $this->created);
                            case ':day':
                                return strftime('%d', $this->created);
                            default:
                                return $part;
                        }
                    }, $parts));

                    return \Idno\Core\Idno::site()->config()->getDisplayURL() . ltrim($path, '/');
                }

                $new = false;
                if ($args = func_get_args()) {
                    if ($args[0] === true) {
                        $new = true;
                    }
                }

                $id = $this->getID();
                if (!$new && !empty($id)) {
                    $uuid = $this->getUUID();
                    if (!empty($uuid)) {
                        return $uuid;
                    }
                }

                return $this->getEditURL();

            }

            /**
             * Returns the URL of this object, or the URL of the contained object if this is a container.
             * @return string
             */
            function getObjectURL()
            {
                return $this->getURL();
            }

            /**
             * Wrapper for getURL
             * @return string
             */
            function getDisplayURL()
            {
                $url = $this->getURL();
                if (\Idno\Core\Idno::site()->config()->unique_urls) {
                    $url = \Idno\Core\Idno::site()->template()->getURLWithVar('rnd', rand(0, 999999), $url);
                }

                return $url;
            }

            /**
             * Wrapper for getURL, specifically for syndication
             * @return string
             */
            function getSyndicationURL()
            {
                return $this->getDisplayURL();
            }

            /**
             * Many properties in mf2 can have either a simple string value or a complex
             * object value, "u-in-reply-to h-cite" is a common example. This function
             * takes a possibly mixed array, and returns an array of only strings.
             *
             * @return array
             */
            static function getStringURLs($arr)
            {
                $result = [];
                foreach ($arr as $value) {
                    if (is_string($value)) {
                        $result[] = $value;
                    } else if (is_array($value) && !empty($value['properties']) && !empty($value['properties']['url'])) {
                        foreach ($value['properties']['url'] as $url) {
                            $result[] = $url;
                        }
                    }
                }

                return $result;
            }

            /**
             * Add webmentions as annotations based on Microformats 2 data
             *
             * @param string $source The source URL
             * @param string $target The target URL (i.e., the page on this site that was pinged)
             * @param array $source_response The Webservice response from fetching the source page
             * @param array $source_mf2 Parsed Microformats 2 content from $source
             * @return bool Success
             */
            function addWebmentions($source, $target, $source_response, $source_mf2)
            {
                if ($source_response['response'] == 410) {
                    $this->removeAnnotation($source);
                    $this->save();

                    return true;
                }
                $return = false;
                if (!empty($source_mf2) && !empty($source_mf2['items']) && is_array($source_mf2['items'])) {

                    // At this point, we don't know who owns the page or what the content is.
                    // First, we'll initialize some variables that we're interested in filling.

                    $mentions = array('owner' => array(), 'mentions' => array()); // Content owner and usable webmention items

                    // Get the page title from the source content
                    $title = Webmention::getTitleFromContent($source_response['content'], $source);

                    // primary h-entry on the page
                    $primary = Webmention::findRepresentativeHEntry($source_mf2, $source, ['h-entry']);
                    $author  = Webmention::findAuthorHCard($source_mf2, $source, $primary);

                    // Convert the h-entry to one or more mentions
                    if ($primary) {
                        $mentions = $this->addWebmentionItem($primary, $author, $mentions, $source, $target, $title);
                    }

                    if (!empty($mentions['mentions']) && !empty($mentions['owner'])) {
                        $return = true;
                        if (empty($mentions['owner']['photo'])) {
                            $mentions['owner']['photo'] = '';
                        }
                        if (empty($mentions['owner']['url'])) {
                            $mentions['owner']['url'] = $source;
                        }
                        if (empty($mentions['owner']['name'])) {
                            $mentions['owner']['name'] = 'Web user';
                        }
                        if (empty($mentions['title'])) {
                            $mentions['title'] = '';
                        }
                        $this->removeAnnotation($source);

                        foreach ($mentions['mentions'] as $mention) {
                            if (!empty($mention['url'])) {
                                $permalink = $mention['url'][0]; //implode('', $mention['url']);
                            } else {
                                $permalink = $source;
                            }

                            // Special exemption for bridgy
                            if ((strpos($source, 'https://brid-gy.appspot.com/') !== false) && in_array($mention['type'], array('like', 'share', 'rsvp'))) {
                                $permalink = \Idno\Core\Idno::site()->template()->getURLWithVar('known_from', $source, implode('', $mention['url']));
                            }
                            if (!$this->addAnnotation($mention['type'], $mentions['owner']['name'], $mentions['owner']['url'], $mentions['owner']['photo'], $mention['content'], $permalink, $mention['created'], $mention['title'])) {
                                $return = false;
                            }
                        }
                        $this->save();

                        if ($return && $this->isReply()) {
                            if ($reply_urls = $this->getReplyToURLs()) {
                                foreach ($reply_urls as $reply_url) {
                                    Webmention::sendWebmentionPayload($this->getDisplayURL(), $reply_url);
                                }
                            }
                        }
                    }
                }

                return $return;
            }

            /**
             * Removes the annotation with the URL $annotation_url from this entity
             * @param string $annotation_url Annotation URL to remove
             */
            function removeAnnotation($annotation_url)
            {
                if (!empty($this->annotations)) {
                    $annotations = $this->annotations;
                    if (is_array($annotations)) {
                        foreach ($annotations as $subtype => $array) {
                            if (is_array($array)) {
                                // try to remove the annotation by its local /annotation url
                                if (array_key_exists($annotation_url, $array)) {
                                    unset($annotations[$subtype][$annotation_url]);
                                    $this->annotations = $annotations;

                                    return true;
                                }
                                // try to remove the annotation by its source permalink
                                foreach ($array as $local_url => $properties) {
                                    if (isset($properties['permalink']) && $properties['permalink'] === $annotation_url) {
                                        unset($annotations[$subtype][$local_url]);
                                        $this->annotations = $annotations;

                                        return true;
                                    }
                                }
                            }
                        }
                    }
                }

                return false;
            }

            /**
             * Recursive helper function for parsing webmentions.
             *
             * @param array $item h-entry to interpret
             * @param array $author h-card for the author of $item
             * @param array $mentions
             * @return array the modified mentions array
             */
            function addWebmentionItem($item, $author, $mentions, $source, $target, $title = '')
            {
                if ($author) {
                    $mentions['owner'] = $this->parseHCard($author);
                }

                if ($item) {
                    $mention = array();
                    if (!empty($item['properties'])) {
                        if (!empty($item['properties']['content'])) {
                            $mention['content'] = '';
                            if (is_array($item['properties']['content'])) {
                                foreach ($item['properties']['content'] as $content) {
                                    if (!empty($content['value'])) {
                                        $parsed_content = \Idno\Core\Idno::site()->template()->sanitize_html($content['value']);
                                        if (!substr_count($mention['content'], $parsed_content)) {
                                            $mention['content'] .= $parsed_content;
                                        }
                                    }
                                }
                            } else {
                                $mention['content'] = $item['properties']['content'];
                            }
                        } else if (!empty($item['properties']['summary'])) {
                            // TODO properties are always arrays, are these checks unnecessary?
                            if (is_array($item['properties']['summary'])) {
                                $mention['content'] = \Idno\Core\Idno::site()->template()->sanitize_html(implode(' ', $item['properties']['summary']));
                            } else {
                                $mention['content'] = $item['properties']['summary'];
                            }
                        } else if (!empty($item['properties']['name'])) {
                            if (is_array($item['properties']['name'])) {
                                $mention['content'] = \Idno\Core\Idno::site()->template()->sanitize_html(implode(' ', $item['properties']['name']));
                            } else {
                                $mention['content'] = $item['properties']['name'];
                            }
                        }
                        if (!empty($item['properties']['published'])) {
                            $mention['created'] = strtotime($item['properties']['published'][0]);
                        }
                        if (empty($mention['created'])) {
                            $mention['created'] = time();
                        }
                        if (!empty($item['properties']['url'])) {
                            if (!empty($item['properties']['uid'])) {
                                $mention['url'] = array_intersect($item['properties']['uid'], $item['properties']['url']);
                            }
                            if (empty($mention['url'])) {
                                $mention['url'] = $item['properties']['url'];
                            }
                        }
                        if (!empty($item['properties']['like-of']) && is_array($item['properties']['like-of'])) {
                            if (in_array($target, static::getStringURLs($item['properties']['like-of']))) {
                                $mention['type'] = 'like';
                            }
                        }
                        if (!empty($item['properties']['rsvp']) && is_array($item['properties']['rsvp'])) {
                            $mention['type']    = 'rsvp';
                            $mention['content'] = implode(' ', $item['properties']['rsvp']);
                        }
                        foreach (array('share', 'repost-of') as $verb) {
                            if (!empty($item['properties'][$verb]) && is_array($item['properties'][$verb])) {
                                if (in_array($target, static::getStringURLs($item['properties'][$verb]))) {
                                    $mention['type'] = 'share';
                                }
                            }
                        }
                        if (!empty($item['properties']['in-reply-to']) && is_array($item['properties']['in-reply-to'])) {
                            if (in_array($target, static::getStringURLs($item['properties']['in-reply-to']))) {
                                $mention['type'] = 'reply';
                            }
                        }
                        if (empty($mention['type'])) {
                            $mention['type'] = 'mention';
                        }
                    }
                    if (empty($mention['content'])) {
                        $mention['content'] = '';
                    }
                    $mention['title'] = $title;
                    if (!empty($mention['type'])) {
                        $mentions['mentions'][] = $mention;
                    }

                }

                return $mentions;
            }

            private function parseHCard($hcard)
            {
                $owner = [];
                if (!empty($hcard['properties']['name'])) {
                    $owner['name'] = $hcard['properties']['name'][0];
                }
                if (!empty($hcard['properties']['url'])) {
                    $owner['url'] = $hcard['properties']['url'][0];
                }
                if (!empty($hcard['properties']['photo'])) {
                    //$owner['photo'] = $hcard['properties']['photo'][0];

                    $tmpfname = tempnam(sys_get_temp_dir(), 'webmention_avatar');
                    file_put_contents($tmpfname, \Idno\Core\Webservice::file_get_contents($hcard['properties']['photo'][0]));

                    $name = md5($hcard['properties']['url'][0]);

                    // TODO: Don't update the cache image for every webmention

                    if ($icon = \Idno\Entities\File::createThumbnailFromFile($tmpfname, $name, 300)) {
                        $owner['photo'] = \Idno\Core\Idno::site()->config()->url . 'file/' . (string)$icon;
                    } else if ($icon = \Idno\Entities\File::createFromFile($tmpfname, $name)) {
                        $owner['photo'] = \Idno\Core\Idno::site()->config()->url . 'file/' . (string)$icon;
                    }

                    unlink($tmpfname);
                }

                return $owner;
            }

            /**
             * Adds an annotation to the entity.
             * @param string $subtype Annotation subtype. 'comment' etc.
             * @param string $owner_name Name of the annotation's owner
             * @param string $owner_url Annotation owner's URL
             * @param string $owner_image Annotation owner's image, if one exists (include a blank string otherwise)
             * @param string $content Content of the annotation
             * @param string|null $annotation_url If included, the existing URL of this annotation
             * @param int $time The UNIX timestamp associated with this annotation (if set to 0, as is default, will be current time)
             * @param string $title The title associated with this annotation (blank by default)
             * @param bool $send_notification Should this call trigger a notifiation? (Default: yes)
             * @return bool Depending on success
             */
            function addAnnotation($subtype, $owner_name, $owner_url, $owner_image, $content, $annotation_url = null, $time = null, $title = '', $send_notification = true)
            {
                if (empty($subtype)) return false;
                if (empty($annotation_url)) {
                    $annotation_url = $this->getURL() . '/annotations/' . md5(time() . $content); // Invent a URL for this annotation
                }
                $post_existed = false;
                if ($existing_annotations = $this->getAnnotations($subtype)) {
                    foreach ($existing_annotations as $existing_local_url => $existing_annotation) {
                        if ($existing_annotation['permalink'] == $annotation_url) {
                            $local_url    = $existing_local_url;
                            $post_existed = true;
                        }
                    }
                }
                if (empty($local_url)) {
                    $local_url = $this->getURL() . '/annotations/' . md5(time() . $content); // Invent a URL for this annotation if we don't have one already
                }
                if (empty($time)) {
                    $time = time();
                } else {
                    $time = (int)$time;
                }
                $annotation  = array('permalink' => $annotation_url, 'owner_name' => $owner_name, 'owner_url' => $owner_url, 'owner_image' => $owner_image, 'content' => $content, 'time' => $time, 'title' => $title);
                $annotations = $this->annotations;
                if (empty($annotations)) {
                    $annotations = array();
                }
                if (empty($annotations[$subtype])) {
                    $annotations[$subtype] = array();
                }

                // Ask whether it's ok to save this annotation (allows filtering)
                if (!\Idno\Core\Idno::site()->triggerEvent('annotation/save', array('annotation' => $annotation, 'object' => $this))) {
                    return false; // Something prevented the annotation from being saved.
                }

                $annotations[$subtype][$local_url] = $annotation;
                $this->annotations                 = $annotations;
                $this->save();

                \Idno\Core\Idno::site()->triggerEvent('annotation/add/' . $subtype, array('annotation' => $annotation, 'object' => $this));

                if ($recipients = $this->getAnnotationOwnerUUIDs(true)) {
                    $recipients[] = $this->getOwnerID();
                    $recipients   = array_unique($recipients);
                } else {
                    $recipients = array($this->getOwnerID());
                }

                if ($send_notification) {
                    foreach ($recipients as $recipient_uuid) {

                        if (Idno::site()->session()->isLoggedIn()) {
                            if ($recipient_uuid == Idno::site()->session()->currentUserUUID()) {
                                // Don't bother sending a notification to the user performing the action
                                // Note: for received webmentions, no user will ever be logged in, so this only applies to local comments
                                continue;
                            }
                        }
                        // Don't send a notification to the commenter
                        if ($recipient_uuid === $owner_url) {
                            continue;
                        }

                        if ($recipient = User::getByUUID($recipient_uuid)) {

                            $send = true;
                            switch ($subtype) {
                                case 'mention':
                                case 'reply':
                                    if ($recipient_uuid == $this->getOwnerID()) {
                                        $subject = $owner_name . ' replied to your post!';
                                    } else {
                                        $subject = $owner_name . ' replied!';
                                    }
                                    $notification_template = 'content/notification/reply';
                                    $context               = 'reply';
                                    break;
                                case 'like':
                                    if ($recipient_uuid == $this->getOwnerID()) {
                                        $subject = $owner_name . ' liked your post!';
                                    } else {
                                        $send = false;
                                    }
                                    $notification_template = 'content/notification/like';
                                    $context               = 'like';
                                    break;
                                case 'share':
                                    if ($recipient_uuid == $this->getOwnerID()) {
                                        $subject = $owner_name . ' reshared your post!';
                                    } else {
                                        $send = false;
                                    }
                                    $notification_template = 'content/notification/share';
                                    $context               = 'share';
                                    break;
                                case 'rsvp':
                                    $subject               = $owner_name . ' RSVPed!';
                                    $notification_template = 'content/notification/rsvp';
                                    $context               = 'rsvp';
                                    break;
                            }

                            if (
                                $send == true && $post_existed == false
                            ) {
                                if (empty($subject)) {
                                    $subject = '';
                                }

                                if (!empty($notification_template) && !empty($context) && $send_notification) {
                                    $notif = new \Idno\Entities\Notification();
                                    if ($notif->setNotificationKey([$context, $recipient->getUUID(), $annotation_url])) {
                                        $notif->setOwner($recipient);
                                        $notif->setMessage($subject);
                                        $notif->setMessageTemplate($notification_template);
                                        $notif->setActor($owner_url);
                                        $notif->setVerb($context);
                                        $notif->setObject($annotation);
                                        $notif->setTarget($this);
                                        $notif->read = false;
                                        $notif->save();
                                        $recipient->notify($notif);
                                    }
                                }
                            }

                        }
                    }
                }

                return true;
            }

            /**
             * Has the specified user annotated this entity?
             * @param $subtype
             * @param bool $owner_url
             * @return bool
             */
            function hasAnnotated($subtype, $owner_url = false)
            {
                if (!$owner_url) {
                    if ($owner = Idno::site()->session()->currentUser()) {
                        $owner_url = $owner->getDisplayURL();
                    }
                }
                if (!$owner_url) return false;
                if ($annotations = $this->getAnnotations($subtype)) {
                    foreach ($annotations as $annotation) {
                        if ($annotation['owner_url'] == $owner_url) return true;
                    }
                }

                return false;
            }

            /**
             * Determines whether the current user can edit the specified annotation
             * @param array|string $annotation
             * @return bool
             */
            function canEditAnnotation($annotation)
            {
                if ($this->canEdit()) {
                    return true;
                }
                if ($user = \Idno\Core\Idno::site()->session()->currentUser()) {
                    if (!is_array($annotation)) {
                        $annotation = $this->getAnnotation($annotation);
                    }
                    if (!empty($annotation['owner_url'])) {
                        if ($annotation['owner_url'] == $user->getURL()) {
                            return true;
                        }
                    }
                }

                return false;
            }

            /**
             * Retrieve an annotation type via its id
             * @param type $uuid
             */
            function getAnnotationSubtype($uuid)
            {
                if (!empty($this->annotations) && is_array($this->annotations)) {
                    foreach ($this->annotations as $subtype => $array) {
                        if (isset($array[$uuid]))
                            return $subtype;
                    }
                }

                return false;
            }

            /**
             * Retrieve an annotation via its id
             * @param type $uuid
             */
            function getAnnotation($uuid)
            {
                if (!empty($this->annotations) && is_array($this->annotations)) {
                    foreach ($this->annotations as $subtype => $array) {
                        if (isset($array[$uuid]))
                            return $array[$uuid];
                    }
                }

                return false;
            }

            /**
             * Return all the annotations on this entity of a specific subtype. If there are no annotations of
             * this subtype, an empty array will be returned.
             *
             * @param string $subtype The type of annotation. eg, 'comment'
             * @return array
             */
            function getAnnotations($subtype)
            {
                if (!empty($this->annotations) && is_array($this->annotations) && !empty($this->annotations[$subtype])) {
                    return $this->annotations[$subtype];
                }

                return array();
            }

            /**
             * Retrieves all annotations associated with this object
             * @return array
             */
            function getAllAnnotations()
            {
                if (!empty($this->annotations) && is_array($this->annotations)) {
                    return $this->annotations;
                }

                return array();
            }

            /**
             * Count the number of annotations of a particular subtype this entity has
             *
             * @param string $subtype Annotation type (eg 'comments')
             * @return int
             */
            function countAnnotations($subtype)
            {
                if (!empty($this->annotations) && is_array($this->annotations) && !empty($this->annotations[$subtype])) {
                    return sizeof($this->annotations[$subtype]);
                }

                return 0;
            }

            /**
             * Retrieves a list of UUIDs of annotation owners
             * @param bool $local If set to true, only returns UUIDs of users who belong to this Known site
             * @return array
             */
            function getAnnotationOwnerUUIDs($local = false)
            {
                $owners = array();
                if (!empty($this->annotations)) {
                    foreach ($this->annotations as $annotation_type) {
                        if (!empty($annotation_type) && is_array($annotation_type)) {
                            foreach ($annotation_type as $annotation) {
                                if (!empty($annotation['owner_url'])) {
                                    if ((parse_url($annotation['owner_url'], PHP_URL_HOST) == parse_url(\Idno\Core\Idno::site()->config()->getURL(), PHP_URL_HOST)) || !$local) {
                                        $owners[] = $annotation['owner_url'];
                                    }
                                }
                            }
                        }
                    }
                }

                return $owners;
            }

            /**
             * Allows you to query for a property value as you would an array
             * @param mixed $offset
             * @return bool
             */
            function offsetExists($offset)
            {
                return empty($this->attributes[$offset]);
            }

            /**
             * Allows you to retrieve a property value as you would an array
             * @param mixed $offset
             * @return mixed
             */
            function offsetGet($offset)
            {
                return $this->attributes[$offset];
            }

            /**
             * Allows you to set a property value as you would an array
             * @param mixed $offset
             * @param mixed $value
             */
            function offsetSet($offset, $value)
            {
                $this->attributes[$offset] = $value;
            }

            /**
             * Allows you to unset a property value as you would an array
             * @param mixed $offset
             */
            function offsetUnset($offset)
            {
                unset($this->attributes[$offset]);
            }

            /**
             * Retrieve this object's stored attributes
             * @return array
             */
            function getAttributes()
            {
                return $this->attributes;
            }

        }

    }
