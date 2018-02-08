<?php


namespace Idno\Core {
    
    /**
     * Register translation strings for a specific language, based on a massive array of values.
     * 
     * This is the simplest way to register translations, using an associative array of "key" => "value"
     */
    abstract class ArrayKeyTranslation 
        extends Translation
    {
        
        public function getString($key) {
            $keys = $this->getStrings();
            
            if (array_key_exists($key, $keys)) {
                return $keys[$key];
            }
                    
        }
        
        /**
         * Return all of the strings.
         */
        abstract public function getStrings();
        
    }
    
}