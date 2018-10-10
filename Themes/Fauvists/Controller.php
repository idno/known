<?php

namespace Themes\Fauvists {

    class Controller extends \Idno\Common\Theme
    {
        function registerTranslations()
        {

            \Idno\Core\Idno::site()->language()->register(
                new \Idno\Core\GetTextTranslation(
                    'fauvists', dirname(__FILE__) . '/languages/'
                )
            );
        }
    }

}

