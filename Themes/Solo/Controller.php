<?php

namespace Themes\Solo {

    class Controller extends \Idno\Common\Theme
    {
        function registerTranslations()
        {

            \Idno\Core\Idno::site()->language()->register(
                new \Idno\Core\GetTextTranslation(
                    'solo', dirname(__FILE__) . '/languages/'
                )
            );
        }
    }

}

