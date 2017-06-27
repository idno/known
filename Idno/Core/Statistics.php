<?php

    namespace Idno\Core {

        use Idno\Common\Component;

        class Statistics extends Component
        {
            public static function basic() {
                $basics = [
                    'Users' => \Idno\Entities\User::count()
                ];
                
                $types = \Idno\Common\ContentType::getRegisteredClasses(); 
                foreach ($types as $type) {
                    
                    $basics[$type] = call_user_func([$type, 'count']);
                    
                }
                
                return $basics;
            }
            
            /**
             * Gather statistics.
             * @return array
             */
            public static function gather() {
                $stats = [
                    'Basic' => static::basic(),
                    
                ];
                
                return \Idno\Core\Idno::site()->triggerEvent('statistics/gather', [], $stats);
            }

        }

    }