<?php

    /**
     * Firefox sidebar
     */

    namespace IdnoPlugins\Firefox\Pages {

        /**
         * Default class to serve Firefox-related account settings
         */
        class Worker extends \Idno\Common\Page
        {

            function getContent()
            {
                $t = \Idno\Core\site()->template();
                echo $t->draw('firefox/worker');
            }

        }
    }