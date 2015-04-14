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
                if ($messages = \Idno\Core\site()->getVendorMessages()) {
                    \Idno\Core\site()->session()->addMessage($messages);
                }
                $t        = \Idno\Core\site()->template();
                $t->body  = $t->__(array('vendor_messages' => $messages))->draw('admin/home');
                $t->title = 'Administration';
                $t->drawPage();

            }

            function postContent()
            {
                $this->adminGatekeeper(); // Admins only
                $title                = $this->getInput('title');
                $description          = $this->getInput('description');
                $url                  = rtrim($this->getInput('url'), ' /') . '/';
                $path                 = dirname(dirname(dirname(dirname(__FILE__)))); // Path is more safely derived from install location
                if (!empty($url)) {
                    $host                 = parse_url($url, PHP_URL_HOST); // Host can be safely derived from URL
                }
                $hub                  = $this->getInput('hub'); // PuSH hub
                $open_registration    = $this->getInput('open_registration');
                $walled_garden        = $this->getInput('walled_garden'); // Private site?
                $indieweb_citation    = $this->getInput('indieweb_citation');
                $indieweb_reference   = $this->getInput('indieweb_reference');
                $user_avatar_favicons = $this->getInput('user_avatar_favicons');
                $wayback_machine      = $this->getInput('wayback_machine');
                $items_per_page       = (int)$this->getInput('items_per_page');
                if ($open_registration == 'true') {
                    $open_registration = true;
                } else {
                    $open_registration = false;
                }
                if ($walled_garden == 'true' && \Idno\Core\site()->config()->canMakeSitePrivate()) {
                    $walled_garden = true;
                } else {
                    $walled_garden = false;
                }
                if ($indieweb_citation == 'true') {
                    $indieweb_citation = true;
                } else {
                    $indieweb_citation = false;
                }
                if ($indieweb_reference == 'true') {
                    $indieweb_reference = true;
                } else {
                    $indieweb_reference = false;
                }
                if ($user_avatar_favicons == 'true') {
                    $user_avatar_favicons = true;
                } else {
                    $user_avatar_favicons = false;
                }
                if ($wayback_machine == 'true') {
                    $wayback_machine = true;
                } else {
                    $wayback_machine = false;
                }
                if (!empty($title)) \Idno\Core\site()->config->config['title'] = $title;
                if (!empty($description)) \Idno\Core\site()->config->config['description'] = $description;
                if (!empty($url)) \Idno\Core\site()->config->config['url'] = $url;
                if (!empty($path)) \Idno\Core\site()->config->config['path'] = $path;
                if (!empty($host)) \Idno\Core\site()->config->config['host'] = $host;
                if (!empty($hub)) \Idno\Core\site()->config->config['hub'] = $hub;
                if (!empty($items_per_page) && is_int($items_per_page)) \Idno\Core\site()->config->config['items_per_page'] = $items_per_page;
                \Idno\Core\site()->config->config['open_registration']    = $open_registration;
                \Idno\Core\site()->config->config['walled_garden']        = $walled_garden;
                \Idno\Core\site()->config->config['indieweb_citation']    = $indieweb_citation;
                \Idno\Core\site()->config->config['indieweb_reference']   = $indieweb_reference;
                \Idno\Core\site()->config->config['user_avatar_favicons'] = $user_avatar_favicons;
                \Idno\Core\site()->config->config['wayback_machine']      = $wayback_machine;
                \Idno\Core\site()->config()->save();
                $this->forward(\Idno\Core\site()->config()->getURL() . 'admin/');
            }

        }

    }
