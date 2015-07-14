<?php

    /**
     * FooterJS administration
     */

    namespace IdnoPlugins\FooterJS\Pages {

        /**
         * Default class to serve Facebook settings in administration
         */
        class Admin extends \Idno\Common\Page
        {

            function getContent()
            {
                $this->adminGatekeeper(); // Admins only
                $t = \Idno\Core\site()->template();
                $body = $t->draw('admin/footerjs');
                $t->__(array('title' => 'Footer Javascript', 'body' => $body))->drawPage();
            }

            function postContent() {
                $this->adminGatekeeper(); // Admins only
                $footerjs = $this->getInput('footerjs');
                $headerjs = $this->getInput('headerjs');
                \Idno\Core\site()->config->config['footerjs'] = $footerjs;
                \Idno\Core\site()->config->config['headerjs'] = $headerjs;
                \Idno\Core\site()->config()->save();
                \Idno\Core\site()->session()->addMessage('Your Header & Footer Javascript details were saved.');
                $this->forward(\Idno\Core\site()->config()->getDisplayURL() . 'admin/footerjs/');
            }

        }

    }