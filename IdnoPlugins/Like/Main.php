<?php

namespace IdnoPlugins\Like {

    class Main extends \Idno\Common\Plugin
    {
        function registerPages()
        {
            \Idno\Core\Idno::site()->routes()->addRoute('/like/edit/?', '\IdnoPlugins\Like\Pages\Edit');
            \Idno\Core\Idno::site()->routes()->addRoute('/like/callback/?', '\IdnoPlugins\Like\Pages\Callback');
            \Idno\Core\Idno::site()->routes()->addRoute('/like/edit/:id/?', '\IdnoPlugins\Like\Pages\Edit');
            \Idno\Core\Idno::site()->routes()->addRoute('/like/delete/:id/?', '\IdnoPlugins\Like\Pages\Delete');
            \Idno\Core\Idno::site()->routes()->addRoute('/bookmark/:id/.*', '\Idno\Pages\Entity\View');
        }

        function registerTranslations()
        {

            \Idno\Core\Idno::site()->language()->register(
                new \Idno\Core\GetTextTranslation(
                    'like', dirname(__FILE__) . '/languages/'
                )
            );
        }
    }

}

