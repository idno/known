<?php

namespace Themes\Kandinsky {

    class Controller extends \Idno\Common\Theme
    {
        function registerTranslations()
        {

            \Idno\Core\Idno::site()->language()->register(
                new \Idno\Core\GetTextTranslation(
                    'kandinsky', dirname(__FILE__) . '/languages/'
                )
            );
        }
    }

}

