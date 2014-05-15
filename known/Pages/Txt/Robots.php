<?php

    /**
     * Robots.txt
     */

    namespace known\Pages\Txt {

        /**
         * Default class to serve the homepage
         */
        class Robots extends \known\Common\Page
        {

            function getContent()
            {
                $t = \known\Core\site()->template();
                echo $t->draw('txt/robots');
            }

        }

    }