<?php

    /**
     * All Known theme controllers should extend this component.
     *
     * @package    idno
     * @subpackage core
     */

namespace Idno\Common {

    class Theme extends Component
    {

        function init()
        {
            $result = parent::init();

            return $result;
        }

        function registerLibraries()
        {

            $plugin = new \ReflectionClass(get_called_class());

            $file = $plugin->getFileName();

            if (file_exists(dirname($file) . '/vendor/autoload.php')) {
                include_once dirname($file) . '/vendor/autoload.php';
            }
        }
    }

}

