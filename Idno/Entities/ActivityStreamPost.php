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

        class ActivityStreamPost extends \Idno\Common\Entity
        {

            /**
             * Get activity streams posts by object UUID
             * @param $uuid
             * @return array|bool
             */
            static function getByObjectUUID($uuid)
            {
                if ($result = self::get(array('object' => $uuid), array(), 10000)) {
                    return $result;
                }

                return false;
            }

            /**
             * Describes the entity that performed the activity.
             * @param \Idno\Common\Entity $actor
             */
            function setActor(\Idno\Common\Entity $actor)
            {
                $this->actor = $actor->getUUID();
            }

            /**
             * Identifies the action that the activity describes.
             * Verbs should be one of the established ActivityStreams verb
             * types.
             *
             * @param string $verb
             */
            function setVerb($verb)
            {
                $this->verb = $verb;
            }

            /**
             * Describes the primary object of the activity.
             * @param \Idno\Common\Entity $object
             */
            function setObject(\Idno\Common\Entity $object)
            {
                $this->object = $object->getUUID();

                if (!empty($object->created)) {
                    $this->created = $object->created;
                }
            }

            /**
             * Returns the object UUID associated with this post
             * @return \Idno\Common\Entity
             */
            function getObjectUUID()
            {
                return $this->object;
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
            function setTarget(\Idno\Common\Entity $target)
            {
                $this->target = $target->getUUID();
            }

            /**
             * Get the target object associated with this entry
             * @return \Idno\Common\Entity
             */
            function getTarget()
            {
                return \Idno\Common\Entity::getByUUID($this->target);
            }

            /**
             * Activity streams objects don't in themselves have an activity stream object type.
             * Infinite recursive loops are not our friends.
             * @return false
             */
            function getActivityStreamsObjectType()
            {
                return false;
            }

            /**
             * Converts Idno entities into ActivityStreams objects
             *
             * @param \Idno\Common\Entity $entity
             * @return array
             */
            function entityToActivityStreamsObject(\Idno\Common\Entity $entity)
            {

                $object = array();
                $owner  = $entity->getOwnerID();
                if (!empty($owner) && $owner != $entity->getUUID()) $object['author'] = $this->entityToActivityStreamsObject($entity->getOwner());
                $object['displayName'] = $entity->getTitle();
                $object['id']          = $entity->getUUID();
                $object['objectType']  = $entity->getActivityStreamsObjectType();
                $object['published']   = date('Y-m-d\TH:i:sP', $entity->created);
                $object['updated']     = date('Y-m-d\TH:i:sP', $entity->updated);
                $object['url']         = $entity->getURL();

                return $object;

            }

            /**
             * No need for a friendly slug for activity streams
             * @param string $slug
             * @param int $max_pieces
             * @return string
             */
            function setSlugResilient($slug, $max_pieces = 10)
            {
                $slug       = md5(time() . rand(0, 9999));
                $this->slug = $slug;

                return $slug;
            }

            /**
             * Pass webmentions to the component objects
             *
             * @param string $source The source URL
             * @param string $target The target URL (i.e., the page on this site that was pinged)
             * @param string $source_content The source page's HTML
             * @param array $source_mf2 Parsed Microformats 2 content from $source
             * @return bool
             */
            function addWebmentions($source, $target, $source_content, $source_mf2)
            {
                if ($object = $this->getObject()) {
                    return $object->addWebmentions($source, $target, $source_content, $source_mf2);
                }

                return parent::addWebmentions($source, $target, $source_content, $source_mf2);
            }

            /**
             * Get the object associated with this stream entry
             * @return \Idno\Common\Entity
             */
            function getObject()
            {
                return \Idno\Common\Entity::getByUUID($this->object);
            }

            /**
             * Retrieves the URL of the contained object if there is one; otherwise the URL of this stream item.
             * @return string
             */
            function getObjectURL()
            {
                if ($object = $this->getObject()) {
                    return $object->getURL();
                }

                return $this->getURL();
            }

            /**
             * Serialize this entity
             * @return array|mixed
             */
            function jsonSerialize()
            {
                $actor         = $this->getActor();
                $object        = $this->getObject();
                $serialization = array(
                    'id'        => $this->getUUID(),
                    'content'   => $this->getTitle(),
                    'title'     => $this->getTitle(),
                    'verb'      => $this->getVerb(),
                    'actor'     => $actor->jsonSerialize(),
                    'object'    => $object->jsonSerialize(),
                    'published' => date(\DateTime::RFC3339, $this->created),
                );

                return $serialization;
            }

            /**
             * Get the actor associated with this entity
             */
            function getActor()
            {
                return \Idno\Common\Entity::getByUUID($this->actor);
            }

            /**
             * Retrieve the verb associated with this activity.
             * @return string
             */
            function getVerb()
            {
                if (!empty($this->verb)) {
                    return $this->verb;
                } else {
                    return 'post';
                }
            }

        }

    }