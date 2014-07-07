<?php

    /**
     * Theme management class
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Core {

        class Themes extends \Idno\Common\Component
        {

            public $theme = ''; // Property containing the current theme (blank if none)
            public $themes = []; // Array containing instantiated theme controllers

            /**
             * On initialization, the theme management class loads the current theme and sets it as
             * a template directory
             */
            public function init()
            {

                if (!empty(site()->config()->theme)) {
                    $this->theme = site()->config()->theme;
                    \Bonita\Main::additionalPath(site()->config()->path . '/Themes/' . $this->theme);
                    if ($config = parse_ini_file(\Idno\Core\site()->config()->path . '/Themes/' . $this->theme . '/theme.ini', true)) {
                        if (!empty($config['extensions'])) {
                            $extensions = $config['extensions'];
                        } else if (!empty($config['Extensions'])) {
                            $extensions = $config['Extensions'];
                        }
                        if (!empty($extensions)) {
                            foreach ($extensions as $template => $extension) {
                                site()->template()->extendTemplate($template, $extension);
                            }
                        }
                        if (is_subclass_of("Themes\\{$this->theme}\\Controller", 'Idno\\Common\\Theme')) {
                            $class                      = "Themes\\{$this->theme}\\Controller";
                            $this->themes[$this->theme] = new $class();
                        }
                    }
                }

            }

            /**
             * Retrieves the array of loaded theme objects
             * @return array
             */
            public function get()
            {
                return $this->theme;
            }

            /**
             * Retrieves a list of stored themes (but not necessarily loaded ones)
             * @return array
             */
            public function getStored()
            {
                $themes = array();
                if ($folders = scandir(\Idno\Core\site()->config()->path . '/Themes')) {
                    foreach ($folders as $folder) {
                        if ($folder != '.' && $folder != '..') {
                            if (file_exists(\Idno\Core\site()->config()->path . '/Themes/' . $folder . '/theme.ini')) {
                                $themes[$folder] = parse_ini_file(\Idno\Core\site()->config()->path . '/Themes/' . $folder . '/theme.ini', true);
                            }
                        }
                    }
                }
                if (file_exists(\Idno\Core\site()->config()->path . 'hosts/'.$_SERVER['HTTP_HOST'].'/Themes')) {
                    if ($folders = scandir(\Idno\Core\site()->config()->path . 'hosts/'.$_SERVER['HTTP_HOST'].'/Themes')) {
                        foreach ($folders as $folder) {
                            if ($folder != '.' && $folder != '..') {
                                if (file_exists(\Idno\Core\site()->config()->path . 'hosts/'.$_SERVER['HTTP_HOST'].'/Themes' . $folder . '/theme.ini')) {
                                    $themes[$folder] = parse_ini_file(\Idno\Core\site()->config()->path . 'hosts/'.$_SERVER['HTTP_HOST'].'/Themes' . $folder . '/theme.ini', true);
                                }
                            }
                        }
                    }
                }

                $themes[''] = [
                    'Theme description' => [
                        'name'         => 'Default theme',
                        'version'      => '0.1',
                        'author'       => "Known",
                        'author_email' => "hello@withknown.com",
                        'author_url'   => "http://withknown.com",
                        'description'  => 'The default Known theme.'
                    ]
                ];

                ksort($themes);

                return $themes;
            }

        }

    }