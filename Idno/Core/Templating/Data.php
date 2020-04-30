<?php

namespace Idno\Core\Templating {
    
    trait Data {

        /**
         * Stores data attributes to be attached to a particular object type
         * @param $objectType An activity streams object type ('article', 'note', 'photo', etc)
         * @param $label
         * @param $value
         */
        function addDataToObjectType($objectType, $label, $value)
        {
            if (empty($this->object_data[$objectType])) {
                $this->object_data[$objectType] = [$label => $value];
            } else {
                $this->object_data[$objectType][$label] = $value;
            }
        }

        /**
         * Returns an array of data attributes to be attached to a particular object type
         * @param $objectType An activity streams object type ('article', 'note', 'photo', etc)
         * @return array
         */
        function getDataForObjectType($objectType)
        {
            if (!empty($this->object_data[$objectType])) return $this->object_data[$objectType];
            return [];
        }

        /**
         * Returns a string of data attributes to be attached to the HTML of a particular object type
         * @param $objectType
         * @return string
         */
        function getDataHTMLAttributesForObjectType($objectType)
        {
            $attributes = [];
            if ($data = $this->getDataForObjectType($objectType)) {
                foreach($data as $label => $value) {
                    $attributes[] = 'data-' . $label . '="'.addslashes($value).'"';
                }
            }
            return implode(' ', $attributes);
        }
        
        /**
         * Get the modified time of a Known file.
         * Primarily used by cache busting, this method returns when a file was last modified.
         * @param type $file The file, relative to the known path.
         */
        public function getModifiedTS($file)
        {
            $file = trim($file, '/ ');
            $path = \Idno\Core\Idno::site()->config()->getPath();
            $ts = filemtime($path . '/' . $file);
            return (int)$ts;
        }
    }
    
} 