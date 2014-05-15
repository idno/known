<?php

    /**
     * Defines the site homepage
     */

    namespace known\Pages {

        /**
         * Default class to serve the homepage
         */
        class Homepage extends \known\Common\Page
        {

            // Handle GET requests to the homepage

            function getContent()
            {

                $query          = $this->getInput('q');
                $offset         = (int)$this->getInput('offset');
                $types          = $this->getInput('types');
                $friendly_types = [];

                if (!empty($this->arguments[0])) { // If we're on the friendly content-specific URL
                    if ($friendly_types = explode('/', $this->arguments[0])) {
                        $friendly_types = array_filter($friendly_types);
                        if (empty($friendly_types) && !empty($query)) {
                            $friendly_types = [all];
                        }
                        $types          = [];
                        // Run through the URL parameters and set content types appropriately
                        foreach ($friendly_types as $friendly_type) {
                            if ($friendly_type == 'all') {
                                $types = \known\Common\ContentType::getRegisteredClasses();
                                break;
                            }
                            if ($content_type_class = \known\Common\ContentType::categoryTitleToClass($friendly_type)) {
                                $types[] = $content_type_class;
                            }
                        }
                    }
                } else {
                    // If user has content-specific preferences, do something with $friendly_types
                    if (empty($query)) {
                        if ($user = $this->getOwner()) {
                            $types = $user->getDefaultContentTypes();
                        }
                    }
                }

                $search = [];

                if (!empty($query)) {
                    $search = \known\Core\site()->db()->createSearchArray($query);
                }

                if (empty($types)) {
                    $types          = 'known\Entities\ActivityStreamPost';
                    $search['verb'] = 'post';
                } else {
                    if (!is_array($types)) $types = [$types];
                    $types[] = '!known\Entities\ActivityStreamPost';
                }

                $count = \known\Entities\ActivityStreamPost::countFromX($types, []);
                $feed  = \known\Entities\ActivityStreamPost::getFromX($types, $search, [], \known\Core\site()->config()->items_per_page, $offset);
                if (\known\Core\site()->session()->isLoggedIn()) {
                    $create = \known\Common\ContentType::getRegistered();
                } else {
                    $create = false;
                }

                if (!empty(\known\Core\site()->config()->description)) {
                    $description = \known\Core\site()->config()->description;
                } else {
                    $description = 'An independent social website, powered by known.';
                }

                $t = \known\Core\site()->template();
                $t->__(array(

                    'title'       => \known\Core\site()->config()->title,
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