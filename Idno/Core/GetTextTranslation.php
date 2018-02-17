<?php

namespace Idno\Core {

    /**
     * Translate via GetText.
     */
    class GetTextTranslation extends Translation {
        
        private $domain;

        /**
         * Create a GetText translation.
         * @param type $domain 'known' for core, 'mypluginname' for plugins
         * @param type $path Full path to where to find the translations.
         * @throws \RuntimeException
         */
        public function __construct(
                $domain = 'known',
                $path = ''
        ) {
            // Empty domain, that's a problem
            if (empty($domain)) {
                throw new \RuntimeException('You must specify a language domain, "known" for core translations, or "yourpluginname", for a plugin');
            }

            // Empty path, so lets set a standard base path
            if (empty($path)) { 
                $path = \Idno\Core\Idno::site()->config()->getPath() . '/languages/';
            }
            
            // Normalise path
            $path = rtrim($path, '/') . '/';
            
            // Normalise domain
            $domain = preg_replace("/[^a-zA-Z0-9\-\_\s]/", "", $domain);
            $this->domain = $domain;

            // Set domain
            bindtextdomain($domain, $path);
            bind_textdomain_codeset($domain, 'UTF-8');
            
        }
        
        public function canProvide($language) {
            return true; // Gettext can always provide translations
        }

        public function getString($key) {
            return dgettext($this->domain, $key); // Get a specific translation, from this object's domain
        }

    }

}