<?php

namespace Themes\Black {

    class Controller extends \Idno\Common\Theme
    {
        function registerTranslations()
        {

            \Idno\Core\Idno::site()->language()->register(
                new \Idno\Core\GetTextTranslation(
                    'black', dirname(__FILE__) . '/languages/'
                )
            );
        }
    }

}

