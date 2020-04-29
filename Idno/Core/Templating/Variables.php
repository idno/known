<?php

namespace Idno\Core\Templating {
    
    trait Variables {
        
        /**
         * Document a form control and make it easily discoverable by the API.
         * @param type $name Name of the control
         * @param type $values Array of form value. Common are 'type', 'description', 'id'
         */
        function documentFormControl($name, $values = [])
        {
            $vars = [];
            if (!empty($this->vars['formFields'])) {
                $vars = $this->vars['formFields'];
            }

            if (isset($vars[$name])) {
                $vars[$name][] = $values;
            } else {
                if (strpos($name, '[')===false)
                    $vars[$name] = $values;
                else
                    $vars[$name][] = $values;
            }

            $this->__(['formFields' => $vars]);
        }
        
        /**
         * Should we render as `h-feed`?
         * @return bool
         */
        public function isHFeed() {
            $classes = \Idno\Core\Idno::site()->template()->getBodyClasses();
            return (strpos($classes, "homepage") || strpos($classes, "page-content") || strpos($classes, "page-tag"));
        }
        
        /**
         * Returns a version of this template with variable defaults set up for the shell
         * @param $vars
         * @return \Idno\Core\Bonita\Templates
         */
        function formatShellVariables($vars)
        {

            // Get instance of current page for use further down the page
            if ($vars['currentPage'] = \Idno\Core\Idno::site()->currentPage()) {
                $vars['pageOwner'] = $vars['currentPage']->getOwner();
            }

            if (!empty($currentPage)) {
                $vars['hidenav'] = \Idno\Core\Idno::site()->embedded();
            }

            if ($tag = \Idno\Core\Idno::site()->template()->getTag()) {
                if (!empty($tag)) {
                    $vars['title'] = '#' . $tag . ' | ' . $vars['title'];
                }
            }

            $vars['description'] = isset($vars['description']) ? $vars['description'] : '';

            if (empty($vars['title']) && !empty($vars['description'])) {
                $vars['title'] = implode(' ', array_slice(explode(' ', strip_tags($vars['description'])), 0, 10));
            }

            // Use appropriate language
            $vars['lang'] = 'en';
            if (!empty(\Idno\Core\Idno::site()->config()->lang)) {
                $vars['lang'] = \Idno\Core\Idno::site()->config()->lang;
            }

            // H-feed vars
            $vars['html_className'] = '';
            $vars['title_className'] = '';
            if ($this->isHFeed()) {
                $vars['html_className'] = ' class="h-feed"';
                $vars['title_className'] = ' class="p-name"';
            }

            if (empty($vars['title'])) $vars['title'] = '';
            if (empty($vars['body'])) $vars['body'] = '';

            return $this->__($vars);
        }
        
        
    }
}
