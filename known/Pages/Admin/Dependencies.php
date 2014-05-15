<?php

    /**
     * Administration page: PHP dependencies
     */

    namespace known\Pages\Admin {

        /**
         * Default class to serve the homepage
         */
        class Dependencies extends \known\Common\Page
        {

            function getContent()
            {
                $this->adminGatekeeper(); // Admins only
                $t        = \known\Core\site()->template();
                $t->body  = $t->draw('admin/dependencies');
                $t->title = 'Dependencies';
                $t->drawPage();

            }

            function postContent()
            {
                $this->adminGatekeeper(); // Admins only
                $title             = $this->getInput('title');
                $url               = $this->getInput('url');
                $path              = $this->getInput('path');
                $host              = $this->getInput('host');
                $open_registration = $this->getInput('open_registration');
                if ($open_registration == 'true') {
                    $open_registration = true;
                } else {
                    $open_registration = false;
                }
                if (!empty($title)) \known\Core\site()->config->config['title'] = $title;
                if (!empty($url)) \known\Core\site()->config->config['url'] = $url;
                if (!empty($path)) \known\Core\site()->config->config['path'] = $path;
                if (!empty($host)) \known\Core\site()->config->config['host'] = $host;
                \known\Core\site()->config->config['open_registration'] = $open_registration;
                \known\Core\site()->config()->save();
                $this->forward('/admin/');
            }

        }

    }