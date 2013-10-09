<?php

    /**
     * Administration page: PHP dependencies
     */

    namespace Idno\Pages\Admin {

        /**
         * Default class to serve the homepage
         */
        class Dependencies extends \Idno\Common\Page
        {

            function getContent()
            {
                $this->adminGatekeeper(); // Admins only
                $t = \Idno\Core\site()->template();
                $t->body = $t->draw('admin/dependencies');
                $t->title = 'Dependencies';
                $t->drawPage();

            }

            function postContent()
            {
                $this->adminGatekeeper();  // Admins only
                $title = $this->getInput('title');
                $url = $this->getInput('url');
                $path = $this->getInput('path');
                $host = $this->getInput('host');
                $open_registration = $this->getInput('open_registration');
                if ($open_registration == 'true') {
                    $open_registration = true;
                } else {
                    $open_registration = false;
                }
                if (!empty($title)) \Idno\Core\site()->config->config['title'] = $title;
                if (!empty($url)) \Idno\Core\site()->config->config['url'] = $url;
                if (!empty($path)) \Idno\Core\site()->config->config['path'] = $path;
                if (!empty($host)) \Idno\Core\site()->config->config['host'] = $host;
                \Idno\Core\site()->config->config['open_registration'] = $open_registration;
                \Idno\Core\site()->config()->save();
                $this->forward('/admin/');
            }

        }

    }