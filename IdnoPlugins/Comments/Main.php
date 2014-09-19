<?php

    namespace IdnoPlugins\Comments {

        use Idno\Common\Plugin;

        class Main extends Plugin
        {

            function RegisterPages()
            {

                \Idno\Core\site()->addPageHandler('/comments/post/?', '\IdnoPlugins\Comments\Pages\Post', true);
                \Idno\Core\site()->template()->extendTemplate('entity/annotations/comment/main', 'comments/public/form');

            }

        }

    }