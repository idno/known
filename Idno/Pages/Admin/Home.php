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
                $lastOptTime = empty(\Idno\Core\Idno::site()->config()->dboptimized) ? 0 : \Idno\Core\Idno::site()->config()->dboptimized;
                if (($time = time()) - $lastOptTime > 24 * 60 * 60) {
                    \Idno\Core\Idno::site()->logging()->info("Optimizing database tables. Last run " . date('Y-m-d H:i:s', $lastOptTime));
                    \Idno\Core\Idno::site()->db()->optimize();
                    \Idno\Core\Idno::site()->config()->dboptimized = $time;
                    \Idno\Core\Idno::site()->config()->save();
                }
                if ($messages = \Idno\Core\Idno::site()->getVendorMessages()) {
                    \Idno\Core\Idno::site()->session()->addMessage($messages);
                }
                $t        = \Idno\Core\Idno::site()->template();
                $t->body  = $t->__(array('vendor_messages' => $messages))->draw('admin/home');
                $t->title = 'Administration';
                $t->drawPage();
            }

            function postContent()
            {
                $this->adminGatekeeper(); // Admins only
                $title         = $this->getInput('title');
                $homepagetitle = $this->getInput('homepagetitle');
                $description   = $this->getInput('description');
                $url           = rtrim($this->getInput('url'), ' /') . '/';
                $path          = dirname(dirname(dirname(dirname(__FILE__)))); // Path is more safely derived from install location
                if (!empty($url)) {
                    $host = parse_url($url, PHP_URL_HOST); // Host can be safely derived from URL
                }
                $hub                  = $this->getInput('hub'); // PuSH hub
                $open_registration    = $this->getInput('open_registration');
                $walled_garden        = $this->getInput('walled_garden');
                $show_privacy         = $this->getInput('show_privacy');
                $indieweb_citation    = $this->getInput('indieweb_citation');
                $indieweb_reference   = $this->getInput('indieweb_reference');
                $user_avatar_favicons = $this->getInput('user_avatar_favicons');
                $wayback_machine      = $this->getInput('wayback_machine');
                $items_per_page       = (int)$this->getInput('items_per_page');
                $permalink_structure  = $this->getInput('permalink_structure');
                if ($open_registration == 'true') {
                    $open_registration = true;
                } else {
                    $open_registration = false;
                }
                if ($walled_garden == 'true' && \Idno\Core\Idno::site()->config()->canMakeSitePrivate()) {
                    $walled_garden = true;
                } else {
                    $walled_garden = false;
                }
                if ($show_privacy == 'true') {
                    $show_privacy = true;
                } else {
                    $show_privacy = false;
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
                if (!empty($title)) \Idno\Core\Idno::site()->config->title = $title;
                \Idno\Core\Idno::site()->config->homepagetitle = trim($homepagetitle);
                if (!empty($description)) \Idno\Core\Idno::site()->config->description = $description;
                if (!empty($url)) \Idno\Core\Idno::site()->config->url = $url;
                if (!empty($path)) \Idno\Core\Idno::site()->config->path = $path;
                if (!empty($host)) \Idno\Core\Idno::site()->config->host = $host;
                \Idno\Core\Idno::site()->config->hub = $hub;
                if (!empty($items_per_page) && is_int($items_per_page)) \Idno\Core\Idno::site()->config->items_per_page = $items_per_page;
                \Idno\Core\Idno::site()->config->open_registration    = $open_registration;
                \Idno\Core\Idno::site()->config->walled_garden        = $walled_garden;
                \Idno\Core\Idno::site()->config->show_privacy         = $show_privacy;
                \Idno\Core\Idno::site()->config->indieweb_citation    = $indieweb_citation;
                \Idno\Core\Idno::site()->config->indieweb_reference   = $indieweb_reference;
                \Idno\Core\Idno::site()->config->user_avatar_favicons = $user_avatar_favicons;
                \Idno\Core\Idno::site()->config->wayback_machine      = $wayback_machine;
                \Idno\Core\Idno::site()->config->permalink_structure  = $permalink_structure;

                \Idno\Core\Idno::site()->triggerEvent('admin/home/save');

                \Idno\Core\Idno::site()->config()->save();
                $this->forward(\Idno\Core\Idno::site()->config()->getURL() . 'admin/');
            }

        }

    }
