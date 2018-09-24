<?php

namespace IdnoPlugins\Comments {

    use Idno\Common\Plugin;

    class Main extends Plugin
    {

        function RegisterPages()
        {

            \Idno\Core\Idno::site()->addPageHandler('/comments/post/?', '\IdnoPlugins\Comments\Pages\Post', true);
            \Idno\Core\Idno::site()->template()->extendTemplate('entity/annotations/comment/main', 'comments/public/form');
        }

        function registerTranslations()
        {

            \Idno\Core\Idno::site()->language()->register(
                new \Idno\Core\GetTextTranslation(
                    'comments', dirname(__FILE__) . '/languages/'
                )
            );
        }

    }

}
