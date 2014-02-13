<?php

    /**
     * Humans.txt
     */

    namespace Idno\Pages\Txt {

        /**
         * Default class to serve the homepage
         */
        class Humans extends \Idno\Common\Page
        {

            function getContent()
            {
                $t = \Idno\Core\site()->template();
                echo $t->draw('txt/humans');
            }

        }

    }