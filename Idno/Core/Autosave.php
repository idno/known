<?php

    /**
     * Site administration
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Core {

        class Autosave extends \Idno\Common\Component
        {

            /**
             * Caches the autosave value for the element $name in the context $context
             * @param string $context
             * @param string $name
             * @param mixed $value
             * @return bool
             */
            function setValue($context, $name, $value) {

                if (site()->session()->isLoggedOn()) {
                    if ($user = site()->session()->currentUser()) {
                        if (!empty($name) && !empty($context)) {
                            $autosave = $user->autosave;
                            $autosave[$context][$name] = $value;
                            $user->autosave = $autosave;
                            return $user->save();
                        }
                    }
                }
                return false;

            }

            /**
             * Retrieves the cached autosave value (if it exists) for $name in the context $context
             * @param string $context
             * @param string $name
             * @param string $default Value to default to if the cache does not exist
             * @return mixed|bool
             */
            function getValue($context, $name, $default = '') {

                if (site()->session()->isLoggedOn()) {
                    if ($user = site()->session()->currentUser()) {
                        if (!empty($user->autosave[$context][$name])) {
                            return $user->autosave[$context][$name];
                        }
                    }
                }
                return $default;

            }

            /**
             * Clears the autosave cache for a particular context
             * @param $context
             * @return bool|false|MongoID|null
             */
            function clearContext($context) {

                if (site()->session()->isLoggedOn()) {
                    if ($user = site()->session()->currentUser()) {
                        $autosave = $user->autosave;
                        $autosave[$context] = [];
                        $user->autosave = $autosave;
                        return $user->save();
                    }
                }
                return false;

            }

        }

    }