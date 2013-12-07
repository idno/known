<?php

    /**
     * Administration page: PHP dependencies
     */

    namespace IdnoPlugins\Styles\Pages\Styles {

        /**
         * Default class to serve the homepage
         */
        class Site extends \Idno\Common\Page
        {

            function getContent()
            {
                $css = '';
                if (!empty(\Idno\Core\site()->config()->styles)) {
                    if (!empty(\Idno\Core\site()->config()->styles['css'])) {
                        $css = \Idno\Core\site()->config()->styles['css'];
                    }
                }

                header('Content-disposition: attachment; filename=style.site.css');
                header('Content-type: text/css');

                echo $css;

            }

        }

    }