<?php

namespace Themes\Twenty26 {

    class Controller extends \Idno\Common\Theme
    {

        function init()
        {
            // Set the modern template type for this theme
            \Idno\Core\Idno::site()->template()->setTemplateType('modern');
        }

    }

}
