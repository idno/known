<?php

    /**
     * Administration page: PHP dependencies
     */

    namespace knownPlugins\Styles\Pages\Styles {

        /**
         * Default class to serve the homepage
         */
        class Site extends \known\Common\Page
        {

            function getContent()
            {
                $css = '';
                if (!empty(\known\Core\site()->config()->styles)) {
                    if (!empty(\known\Core\site()->config()->styles['css'])) {
                        $css = \known\Core\site()->config()->styles['css'];
                    }
                }

                header('Content-disposition: attachment; filename=style.site.css');
                header('Content-type: text/css');

                echo $css;

            }

        }

    }