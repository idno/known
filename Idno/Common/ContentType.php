<?php

    /**
     * All content types should extend this component.
     */

    namespace Idno\Common {

        class ContentType extends Component
        {

            // Property containing the entity class associated with this content type (default is generic object type)
            static public $registered = array();
            public $entity_class = 'Idno\\Entities\\Object';
            public $handler_class = 'Idno\\Common\\ContentType';
            public $title = 'Content type';
            public $indieWebContentType = array();

            // Will this content type show on the creation menu
            public $createable = true;

            /**
             * Given a content type category slug, retrieves its namespaced class name
             * @param $friendly_name
             * @return bool|string
             */
            static function categoryTitleSlugToClass($slug)
            {
                $friendly_name = str_replace(' ', '', trim(strtolower($slug)));
                if ($registered = self::getRegistered()) {
                    foreach ($registered as $contentType) {
                        /* @var ContentType $contentType */
                        $categoryTitle = $contentType->getCategoryTitleSlug();
                        if ($friendly_name == str_replace(' ', '', trim(strtolower($categoryTitle)))) {
                            return $contentType->getEntityClass();
                        }
                    }
                }

                return false;
            }

            /**
             * Get all ContentType objects registered in the system.
             * @return array
             */
            static function getRegistered()
            {
                return self::$registered;
            }

            /**
             * Returns a version of this content type's category title suitable for including in a URL
             * @return string
             */
            function getCategoryTitleSlug()
            {
                return urlencode(strtolower(str_replace(' ', '', $this->getCategoryTitle())));
            }

            /**
             * Describes this content type as a category (eg "photos")
             * @return string
             */
            function getCategoryTitle()
            {
                if (!empty($this->category_title)) {
                    return \Idno\Core\Idno::site()->language()->get($this->category_title);
                }

                return \Idno\Core\Idno::site()->language()->get($this->getTitle());
            }

            /**
             * Return the name of this content type
             * @return string
             */
            function getTitle()
            {
                return \Idno\Core\Idno::site()->language()->get($this->title);
            }

            /**
             * Retrieves the name of the entity class associated with this content type
             * @param bool $convert_slashes If set to true, converts \ slashes to / (false by default)
             * @return string
             */
            function getEntityClass($convert_slashes = false)
            {
                $return = $this->entity_class;
                if ($convert_slashes) {
                    $return = str_replace('\\', '/', $return);
                }

                return $return;
            }

            /**
             * Given a class name, retrieves a content type object
             * @param $class
             * @return bool|ContentType
             */
            static function getContentTypeObjectFromClass($class)
            {
                $friendly_name = str_replace(' ', '', trim(strtolower($class)));
                if ($registered = self::getRegistered()) {
                    foreach ($registered as $contentType) {
                        /* @var ContentType $contentType */
                        if ($contentType->getEntityClass() == $class) {
                            return $contentType;
                        }
                    }
                }

                return false;
            }

            /**
             * Given multiple content types, creates a friendly string describing all of them
             * @param $slugs
             * @return string
             */
            static function categoryTitleSlugsToFriendlyName($slugs)
            {
                if (!is_array($slugs)) {
                    $slugs = explode(', ', $slugs);
                }
                $friendly = array();
                foreach ($slugs as $content) {
                    $friendly[] = self::categoryTitleSlugToFriendlyName($content);
                }

                return implode(', ', $friendly);
            }

            /**
             * Given a content type category slug, retrieves its friendly name
             * @param $slug
             * @return bool|string
             */
            static function categoryTitleSlugToFriendlyName($slug)
            {
                $friendly_name = str_replace(' ', '', trim(strtolower($slug)));
                if ($friendly_name == 'all') {
                    return 'All content';
                }
                if ($registered = self::getRegistered()) {
                    foreach ($registered as $contentType) {
                        /* @var ContentType $contentType */
                        $categoryTitle = $contentType->getCategoryTitleSlug();
                        if ($friendly_name == str_replace(' ', '', trim(strtolower($categoryTitle)))) {
                            return \Idno\Core\Idno::site()->language()->get($contentType->getCategoryTitle());
                        }
                    }
                }

                return false;
            }

            /**
             * Given a content type category name, retrieves its namespaced class name
             * @param $friendly_name
             * @return bool|string
             */
            static function categoryTitleToClass($friendly_name)
            {
                $friendly_name = str_replace(' ', '', trim(strtolower($friendly_name)));
                if ($registered = self::getRegistered()) {
                    foreach ($registered as $contentType) {
                        /* @var ContentType $contentType */
                        $categoryTitle = $contentType->getCategoryTitle();
                        if ($friendly_name == str_replace(' ', '', trim(strtolower($categoryTitle)))) {
                            return $contentType->getEntityClass();
                        }
                    }
                }

                return false;
            }

            /**
             * Register a content type as being available to create / edit
             *
             * @param $class The string name of a class that extends Idno\Common\ContentType.
             * @return bool
             */
            static function register($class)
            {
                if (class_exists($class)) {
                    if (is_subclass_of($class, 'Idno\\Common\\ContentType')) {
                        $contentType        = new $class();
                        self::$registered[] = $contentType;

                        return true;
                    }
                }

                return false;
            }

            /**
             * Get the classes of all entities supplied by ContentType objects registered in the system.
             * @return array
             */
            static function getRegisteredClasses()
            {
                $classes = array();
                if ($registered = self::$registered) {
                    foreach ($registered as $type) {
                        if ($type instanceof ContentType) {
                            $classes[] = $type->getEntityClass();
                        }
                    }
                }

                return $classes;
            }

            /**
             * Get the category title slugs of all entities supplied by ContentType objects registered in the system.
             * @return array
             */
            static function getRegisteredCategorySlugs()
            {
                $slugs = array();
                if ($registered = self::$registered) {
                    foreach ($registered as $type) {
                        if ($type instanceof ContentType) {
                            $slugs[] = $type->getCategoryTitleSlug();
                        }
                    }
                }

                return $slugs;
            }

            /**
             * Given an IndieWeb content type ('note', 'reply', 'rsvp', etc),
             * retrieves the first registered plugin content type that maps to it
             *
             * @param $type
             * @return \Idno\Common\ContentType
             */
            static function getRegisteredForIndieWebPostType($type)
            {
                if ($registered = self::getRegistered()) {
                    foreach ($registered as $contentType) {
                        if (in_array($type, (array)$contentType->indieWebContentType)) {
                            return $contentType;
                        }
                    }
                }

                return false;
            }

            /**
             * Retrieves the icon associated with this content type
             * @param int $width The width of the icon to be returned. (Returned icon may not be the exact width.)
             * @return string The public URL to the content type.
             */
            function getIcon()
            {
                $t      = \Idno\Core\Idno::site()->template();
                $result = $t->draw('entity/' . $this->getEntityClass(true) . '/icon');
                if (!$result) {
                    $result = $t->draw('entity/' . $this->getEntityClassName() . '/icon');
                }

                return $result;
            }

            /**
             * Returns the namespace-free entity class associated with this content type
             * @return string
             */
            function getEntityClassName()
            {
                $class = $this->getEntityClass();

                return substr($class, strrpos($class, '\\') + 1);
            }

            /**
             * Create an object with the entity class associated with this content type
             * @return \Idno\Common\Entity
             */
            function createEntity()
            {
                if (class_exists($this->entity_class)) {
                    $entity = new $this->entity_class();

                    return $entity;
                }

                return false;
            }

            /**
             * Retrieves the URL to the form to create a new object related to this content type
             * @return string
             */
            function getEditURL()
            {
                return \Idno\Core\Idno::site()->config()->url . $this->camelCase($this->getEntityClassName()) . '/edit';
            }

        }

    }