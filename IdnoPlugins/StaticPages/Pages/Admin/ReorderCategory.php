<?php

    namespace IdnoPlugins\StaticPages\Pages\Admin {

        use Idno\Common\Page;

        class ReorderCategory extends Page
        {

            function post()
            {
                $this->adminGatekeeper();

                $category = $this->getInput('category');
                $position = intval($this->getInput('position'));
                if ($staticpages = \Idno\Core\Idno::site()->plugins()->get('StaticPages')) {

                    $categories   = $staticpages->getCategories();
                    $old_position = array_search($category, $categories);
                    if ($old_position === false ||
                        $position < 0 ||
                        $position >= count($categories)
                    ) {

                        // Invalid Request
                        $this->setResponse(400);

                    } else {

                        // Remap categories
                        $new_categories = [];
                        if ($position > $old_position) {
                            foreach ($categories as $k => $v) {
                                if ($k != $old_position) {
                                    $new_categories[] = $v;
                                }
                                if ($k == $position) {
                                    $new_categories[] = $category;
                                }
                            }
                        } else {
                            foreach ($categories as $k => $v) {
                                if ($k == $position) {
                                    $new_categories[] = $category;
                                }
                                if ($k != $old_position) {
                                    $new_categories[] = $v;
                                }
                            }
                        }
                        $staticpages->saveCategories($new_categories);

                        // Accepted
                        $this->setResponse(202);

                    }

                }

            }

        }

    }
