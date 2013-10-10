<?php

    /**
     * Administration homepage
     */

    namespace Idno\Pages\Admin {

        /**
         * Default class to serve the homepage
         */
        class Home extends \Idno\Common\Page
        {

            function getContent()
            {
                $this->adminGatekeeper(); // Admins only
                $t = \Idno\Core\site()->template();
                $t->body = $t->draw('admin/home');
                $t->title = 'Administration';
                $t->drawPage();

            }

            function postContent()
            {
                $this->adminGatekeeper();  // Admins only
                $title = $this->getInput('title');
                $url = $this->getInput('url');
                $path = $this->getInput('path');
                $host = $this->getInput('host');
                $hub = $this->getInput('hub');  // PuSH hub
                $open_registration = $this->getInput('open_registration');
                $items_per_page = (int) $this->getInput('items_per_page');
                if ($open_registration == 'true') {
                    $open_registration = true;
                } else {
                    $open_registration = false;
                }
                if (!empty($title)) \Idno\Core\site()->config->config['title'] = $title;
                if (!empty($url)) \Idno\Core\site()->config->config['url'] = $url;
                if (!empty($path)) \Idno\Core\site()->config->config['path'] = $path;
                if (!empty($host)) \Idno\Core\site()->config->config['host'] = $host;
                if (!empty($hub)) \Idno\Core\site()->config->config['hub'] = $hub;
                if (!empty($items_per_page) && is_int($items_per_page)) \Idno\Core\site()->config->config['items_per_page'] = $items_per_page;
                \Idno\Core\site()->config->config['open_registration'] = $open_registration;
                \Idno\Core\site()->config()->save();
                $this->forward('/admin/');
            }

        }

    }
