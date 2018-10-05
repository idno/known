<?php

namespace Themes\Green {

    class Controller extends \Idno\Common\Theme
    {
        function registerTranslations()
        {

            \Idno\Core\Idno::site()->language()->register(
                new \Idno\Core\GetTextTranslation(
                    'green', dirname(__FILE__) . '/languages/'
                )
            );
        }
    }

}

