<?php

    namespace Idno\Core {

        use Idno\Common\Component;

        class Language extends Component {

            public $strings = [];

            /**
             * Adds a translation to this language's corpus
             * @param $string
             * @param $translation
             * @return bool
             */
            function addTranslation($string, $translation)
            {
                if (!empty($string) && is_string($string)) {
                    $this->strings[$string] = $translation;
                    return true;
                }
                return false;
            }

            /**
             * Shortcut for addTranslation
             * @param $string
             * @param $translation
             * @return bool
             */
            function add($string, $translation)
            {
                return $this->addTranslation($string, $translation);
            }

            /**
             * Retrieves a translation for a given string. If $failover is true (as set by default), the function will
             * return the original string if no translation exists. Otherwise it will return false.
             * @param $string
             * @param bool|true $failover
             * @return string|bool
             */
            function getTranslation($string, $failover = true)
            {
                if (!empty($this->strings[$string])) {
                    return $this->strings[$string];
                }
                if ($failover) {
                    return $string;
                }
                return false;
            }

            /**
             * Shortcut for getTranslation.
             * @param $string
             * @param bool|true $failover
             * @return bool|string
             */
            function get($string, $failover = true)
            {
                return $this->getTranslation($string, $failover);
            }

        }

    }