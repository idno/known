<?php

    /**
     * Administration page: PHP dependencies
     */

    namespace knownPlugins\Styles\Pages {

        /**
         * Default class to serve the homepage
         */
        class Admin extends \known\Common\Page
        {

            function getContent()
            {
                $this->adminGatekeeper(); // Admins only

                $css = '';
                if (!empty(\known\Core\site()->config()->styles)) {
                    if (!empty(\known\Core\site()->config()->styles['css'])) {
                        $css = \known\Core\site()->config()->styles['css'];
                    }
                }

                $t = \known\Core\site()->template();
                $t->__(array(
                            'body'  => $t->__(['css' => $css])->draw('styles/admin'),
                            'title' => 'Site Styles'
                       ))->drawPage();
            }

            function postContent()
            {
                $this->adminGatekeeper(); // Admins only

                $css = $this->getInput('css');
                if (!empty($_FILES['import']['tmp_name'])) {
                    $css = @file_get_contents($_FILES['import']['tmp_name']);
                }
                $css = trim(strip_tags($css));

                $styles                             = ['css' => $css];
                \known\Core\site()->config()->styles = $styles;
                \known\Core\site()->config()->save();
                $this->forward('/admin/styles/');
            }

        }

    }