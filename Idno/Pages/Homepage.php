<?php

    /**
     * Defines the site homepage
     */

    namespace Idno\Pages {

        /**
         * Default class to serve the homepage
         */
        class Homepage extends \Idno\Common\Page
        {

            // Handle GET requests to the homepage

            function getContent()
            {

                $query          = $this->getInput('q');
                $offset         = (int)$this->getInput('offset');
                $types          = $this->getInput('types');
                $friendly_types = array();

                // Check for an empty site
                if (!\Idno\Entities\User::get()) {
                    $this->forward(\Idno\Core\site()->config()->getURL() . 'begin/');
                }

                if (!empty($this->arguments[0])) { // If we're on the friendly content-specific URL
                    if ($friendly_types = explode('/', $this->arguments[0])) {
                        $friendly_types = array_filter($friendly_types);
                        if (empty($friendly_types) && !empty($query)) {
                            $friendly_types = array('all');
                        }
                        $types = array();
                        // Run through the URL parameters and set content types appropriately
                        foreach ($friendly_types as $friendly_type) {
                            if ($friendly_type == 'all') {
                                $types = \Idno\Common\ContentType::getRegisteredClasses();
                                break;
                            }
                            if ($content_type_class = \Idno\Common\ContentType::categoryTitleToClass($friendly_type)) {
                                $types[] = $content_type_class;
                            }
                        }
                    }
                } else {
                    // If user has content-specific preferences, do something with $friendly_types
                    if (empty($query)) {
                        $types = \Idno\Core\site()->config()->getHomepageContentTypes();
                    }
                }

                $search = array();

                if (!empty($query)) {
                    $search = \Idno\Core\site()->db()->createSearchArray($query);
                }

                if (empty($types)) {
                    $types          = 'Idno\Entities\ActivityStreamPost';
                    $search['verb'] = 'post';
                } else {
                    if (!is_array($types)) $types = array($types);
                    $types[] = '!Idno\Entities\ActivityStreamPost';
                }

                $count = \Idno\Entities\ActivityStreamPost::countFromX($types, array());
                $feed  = \Idno\Entities\ActivityStreamPost::getFromX($types, $search, array(), \Idno\Core\site()->config()->items_per_page, $offset);
                if (\Idno\Core\site()->session()->isLoggedIn()) {
                    $create = \Idno\Common\ContentType::getRegistered();
                } else {
                    $create = false;
                }

                if (!empty(\Idno\Core\site()->config()->description)) {
                    $description = \Idno\Core\site()->config()->description;
                } else {
                    $description = 'An independent social website, powered by Known.';
                }

                // If we have a feed, set our last modified flag to the time of the latest returned entry
                if (!empty($feed)) {
                    if (is_array($feed)) {
                        $feed = array_filter($feed);
                        $this->setLastModifiedHeader(reset($feed)->updated);
                    }
                }

                $t = \Idno\Core\site()->template();
                $t->__(array(

                    'title'       => \Idno\Core\site()->config()->title,
                    'description' => $description,
                    'content'     => $friendly_types,
                    'body'        => $t->__(array(
                            'items'        => $feed,
                            'contentTypes' => $create,
                            'offset'       => $offset,
                            'count'        => $count,
                            'subject'      => $query,
                            'content'      => $friendly_types
                        ))->draw('pages/home'),

                ))->drawPage();
            }

        }

    }