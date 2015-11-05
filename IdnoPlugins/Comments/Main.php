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

        }

    }