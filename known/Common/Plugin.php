<?php

    /**
     * All known plugins should extend this component.
     *
     * @package known
     * @subpackage core
     */

    namespace known\Common {

        class Plugin extends Component
        {

            function init()
            {
                $result = parent::init();
                $this->registerContentTypes();
                \Bonita\Main::additionalPath(dirname($this->getFilename()));

                return $result;
            }

            /**
             * Automatically registers content types associated with plugins,
             * as long as they're called knownPlugins\PLUGIN-NAME\ContentType
             */
            function registerContentTypes()
            {
                $namespace = $this->getNamespace();
                if (class_exists($namespace . '\\ContentType')) {
                    if (is_subclass_of($namespace . '\\ContentType', 'known\\Common\\ContentType')) {
                        \known\Common\ContentType::register($namespace . '\\ContentType');
                    }
                }
            }

        }

    }