<?php

    /**
     * Humans.txt
     */

    namespace known\Pages\Txt {

        /**
         * Default class to serve the homepage
         */
        class Humans extends \known\Common\Page
        {

            function getContent()
            {
                $t = \known\Core\site()->template();
                echo $t->draw('txt/humans');
            }

        }

    }