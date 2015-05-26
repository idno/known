<?php

    namespace IdnoPlugins\StaticPages {

        use Idno\Common\Plugin;

        class Main extends Plugin {

            public $cats_and_pages = [];

            function registerPages() {

                \Idno\Core\site()->addPageHandler('/staticpages?/edit/?', 'IdnoPlugins\StaticPages\Pages\Edit');
                \Idno\Core\site()->addPageHandler('/staticpages?/edit/([A-Za-z0-9]+)/?', '\IdnoPlugins\StaticPages\Pages\Edit');
                \Idno\Core\site()->addPageHandler('/staticpages?/delete/([A-Za-z0-9]+)/?', '\IdnoPlugins\StaticPages\Pages\Delete');
                \Idno\Core\site()->addPageHandler('/admin/staticpages/?', 'IdnoPlugins\StaticPages\Pages\Admin');
                \Idno\Core\site()->addPageHandler('/admin/staticpages/add/?', 'IdnoPlugins\StaticPages\Pages\Admin\AddCategory');
                \Idno\Core\site()->addPageHandler('/admin/staticpages/edit/?', 'IdnoPlugins\StaticPages\Pages\Admin\EditCategory');
                \Idno\Core\site()->addPageHandler('/admin/staticpages/delete/?', 'IdnoPlugins\StaticPages\Pages\Admin\DeleteCategory');
                \Idno\Core\site()->addPageHandler('/admin/staticpages/categories/?', 'IdnoPlugins\StaticPages\Pages\Admin\Categories');

                \Idno\Core\site()->addPageHandler('/pages/([A-Za-z0-9\-\_]+)/?', 'IdnoPlugins\StaticPages\Pages\View');

                \Idno\Core\site()->template()->extendTemplate('admin/menu/items', 'staticpages/admin/menu');
                \Idno\Core\site()->template()->prependTemplate('shell/toolbar/links', 'staticpages/toolbar', true);

            }

            /**
             * Save static page categories
             *
             * @param $categories
             * @return bool
             */
            function saveCategories($categories) {

                if (\Idno\Core\site()->session()->isLoggedIn()) {
                    if (\Idno\Core\site()->session()->currentUser()->isAdmin()) {

                        if (is_array($categories)) {
                            $categories = implode("\n",$categories);
                        }
                        \Idno\Core\site()->config->staticPages = ['categories' => $categories];
                        return \Idno\Core\site()->config->save();

                    }
                }
                return false;

            }

            /**
             * Adds a category
             * @param $category
             * @return bool
             */
            function addCategory($category) {

                $category = trim($category);
                if (empty($category)) {
                    return false;
                }
                $categories = $this->getCategories();

                $key = array_search($category, $categories);
                if ($key === false) {
                    $categories[] = $category;
                }

                return $this->saveCategories($categories);

                return false;

            }

            /**
             * Removes a category
             * @param $category
             * @return bool
             */
            function deleteCategory($category) {

                if ($categories = $this->getCategories()) {

                    $key = array_search($category, $categories);
                    if ($key !== false) {
                        if ($pages = $this->getPagesByCategory($category)) {
                            foreach($pages as $page) {
                                $page->category = 'No Category';
                                $page->save();
                            }
                        }
                        unset($categories[$key]);
                    }

                    return $this->saveCategories($categories);

                }

                return false;

            }

            /**
             * Renames a category
             * @param $category
             * @param $new_category
             * @return bool
             */
            function editCategory($category, $new_category) {

                if (empty($category) || empty($new_category)) {
                    return false;
                }
                if ($categories = $this->getCategories()) {
                    $key = array_search($category, $categories);
                    if ($key !== false) {
                        if ($pages = $this->getPagesByCategory($category)) {
                            foreach($pages as $page) {
                                $page->category = $new_category;
                                $page->save();
                            }
                        }
                        $categories[$key] = $new_category;
                    }
                    return $this->saveCategories($categories);
                }

                return false;

            }

            /**
             * Retrieves categories for static pages. You must have categories before you can create a page.
             * @return array
             */
            function getCategories() {

                if (!empty(\Idno\Core\site()->config()->staticPages['categories'])) {
                    // Take the categories record and split it into an array
                    $categories = str_replace("\r",'',\Idno\Core\site()->config()->staticPages['categories']);
                    $categories = explode("\n", $categories);

                    // Trim all categories first
                    array_filter($categories, function($var) {
                        return trim($var);
                    });

                    // Then remove any empty categories
                    array_filter($categories);

                    // Now send back the array
                    return $categories;
                }
                return [];

            }

            /**
             * Given a category, retrieves all pages for that category.
             * @param $category
             * @return array
             */
            function getPagesByCategory($category) {
                return StaticPage::get(['category' => $category]);
            }

            /**
             * Returns a 2d array with categories as the main keys and an array of pages (or false) as the entities
             * @param $force_refresh If set to true, never gets the cached version
             * @return array
             */
            function getPagesAndCategories($force_refresh = false) {

                if (!empty($this->cats_and_pages) && !$force_refresh) {
                    return $this->cats_and_pages;
                }
                $pages = [];
                $categories = $this->getCategories();
                $categories = array_merge(['No Category'], $categories);
                foreach($categories as $category) {
                    $pages[$category] = $this->getPagesByCategory($category);
                }
                $this->cats_and_pages = $pages;
                return $pages;

            }

        }

    }