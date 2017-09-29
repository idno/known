<?php

 namespace Idno\Core {

        class Arrays
        {
            /**
             * Recursively ensure that all entries in 
             * @param array $array
             */
            public static function array_unique_recursive(array $array) {
                
                $return = $array;
                
                foreach ($return as $k => $v) {
                    
                    if (is_array($v)) {
                        $return[$k] = self::array_unique_recursive($v);
                    }
                    
                }
                
                return $return;
            }
            
            /**
             * Replace content in first array with the second.
             * Replace fields in array with those in array2. Unlike with array_replace_recursive, where if an array exists in $array that also exists in 
             * $array2, the first array is replaced entirely, rather than merged.
             * @param type $array
             * @param type $array2
             */
            public static function array_replace_recursive_alt($array, $array2) {
                
            }
            
        }
 }