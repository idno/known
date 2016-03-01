<?php

    /**
     * Generic data storage item.
     * A data item for storing arbitrary data using the Known data handling methods.
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Entities {

        class GenericDataItem extends \Idno\Entities\Object
        {
            /**
             * Retrieve a bit of generic data by it's data type
             * @param type $datatype
             */
            public static function getByDatatype($datatype, $search = array(), $fields = array(), $limit = 10, $offset = 0)
            {
                $search = array_merge($search, ['datatype' => $datatype]);

                return static::getFromX(get_called_class(), $search, $fields, $limit, $offset);
            }

            /**
             * Label this item as being of a user defined type.
             * @param type $datatype
             */
            public function setDatatype($datatype)
            {
                $this->datatype = $datatype;
            }

            public function getDatatype()
            {
                return $this->datatype;
            }

            public function save($add_to_feed = false, $feed_verb = 'post')
            {
                if (empty($this->datatype)) {
                    throw new \RuntimeException("GenericDataItem classes must have a datatype label set.");
                }

                return parent::save($add_to_feed, $feed_verb);
            }
        }

    }