<?php

    /**
     * Administration page: PHP dependencies
     */

    namespace IdnoPlugins\Styles\Pages {

        /**
         * Default class to serve the homepage
         */
        class Admin extends \Idno\Common\Page
        {

            function getContent()
            {
                $this->adminGatekeeper(); // Admins only

                $css = '';
                if (!empty(\Idno\Core\site()->config()->styles)) {
                    if (!empty(\Idno\Core\site()->config()->styles['css'])) {
                        $css = \Idno\Core\site()->config()->styles['css'];
                    }
                }

                $t = \Idno\Core\site()->template();
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
                $config = \Idno\Core\site()->config;
                $config->styles = $styles;
                \Idno\Core\site()->config = $config;
                \Idno\Core\site()->config()->save();
                $this->forward(\Idno\Core\site()->config()->getURL() . 'admin/styles/');
            }

        }

    }