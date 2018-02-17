<?php

namespace Idno\Core {

    use Idno\Common\Component;

    class Language extends Component {

        /**
         * Language associated array of translation objects.
         * @var type 
         */
        private $translations = [];
        
        // @deprecated
        private $strings = [];
        
        /**
         * Current language
         * @var type 
         */
        private $language;

        /**
         * Construct a language object
         * @param type $language
         */
        public function __construct($language = null) {
            $session = \Idno\Core\Idno::site()->session();
            if (!empty($session)) {
                if ($user = \Idno\Core\Idno::site()->session()->currentUser()) {
                    if (!empty($user->language))
                        return $user->language;
                }
            }

            if (empty($language))
                $language = self::detectBrowserLanguage();

            if (empty($language))
                $language = 'en_US';

            $this->language = $language;
            
            // Set locale, now we have one.
            putenv("LANG=" . $language);
            setlocale(LC_ALL, $language);
            
            parent::__construct();
        }

        /**
         * Magic method to set language variables
         * @deprecated Add a Translation object using register
         */
        function __set($string, $translation) {
            if (!empty($string)) {
                $this->add($string, $translation);
            }
        }

        /**
         * Magic method to get stored language variable
         */
        function __get($string) {
            return $this->get($string);
        }

        /**
         * Chainable function to allow variables to be added as an array.
         * @param $vars array Associated array of "string" => "translation"
         * @deprecated Add a Translation object using register
         */
        function __($strings) {
            $this->addTranslations($strings);
        }

        /**
         * Alias for $this->write();
         * @param string $string String to translate
         * @param array $subs Substitutions in the
         */
        function _($string, array $subs = []) {
            return $this->write($string, $subs);
        }
        
        /**
         * Shortcut for addTranslation
         * @param $string
         * @param $translation
         * @return bool
         * @deprecated Add a Translation object using register
         */
        function add($string, $translation) {
            return $this->addTranslation($string, $translation);
        }

        /**
         * Adds a translation to this language's corpus
         * @param $string
         * @param $translation
         * @return bool
         * @deprecated Add a Translation object using register
         */
        function addTranslation($string, $translation) {
            if (!empty($string) && is_string($string)) {
                $this->strings[$string] = $translation;

                return true;
            }

            return false;
        }

        /**
         * Simplify adding translation strings.
         * @param array $strings Associated array of "string" => "translation"
         * @deprecated Add a Translation object using register
         */
        function addTranslations(array $strings) {
            $this->strings = array_merge($this->strings, $strings);
        }
        
        /**
         * Register a translation.
         * Register translation strings. It is safe to provide Translation objects for multiple languages, only translations for
         * $this->getLanguage() will be loaded.
         * @param \Idno\Core\Translation $translation
         */
        public function register(Translation $translation) {
            if ($translation->getLanguage() == $this->getLanguage()) {
                //$this->addTranslations($translation->getStrings());
                $this->translations[] = $translation;
            }
        }
        
        /**
         * Shortcut for getTranslation.
         * @param $string
         * @param bool|true $failover
         * @return bool|string
         */
        function get($string, $failover = true) {
            return $this->getTranslation($string, $failover);
        }

        /**
         * Retrieves a translation for a given string. If $failover is true (as set by default), the function will
         * return the original string if no translation exists. Otherwise it will return false.
         * @param $string
         * @param bool|true $failover
         * @return string|bool
         */
        function getTranslation($string, $failover = true) {
            
            // Look through translation objects
            foreach ($this->translations as $translation) {
                $value = $translation->getString($string);
                if (!empty($value))
                    return $value;
            }
            
            // Look through locally added strings (deprecated).
            if (!empty($this->strings[$string])) {
                return $this->strings[$string];
            }
           
            // If we're in lang_debug mode, lets flag untranslated strings
            if (!empty(\Idno\Core\Idno::site()->config()->lang_debug)) {
                \Idno\Core\Idno::site()->triggerEvent('language/translation/missing-string', [
                    'string' => $string,
                    'language' => $this->language
                ]);
            }
            
            
            if ($failover) {
                return $string;
            }

            return false;
        }

        /**
         * Return the current language code for this object.
         */
        public function getLanguage() {
            return $this->language;
        }
        
        /**
         * Return a translated string, substituting variables in subs in the format of sprintf.
         * @param type $string String to translate
         * @param array $subs List of substitution variables to be used in the translated string
         * @return string
         */
        public function write($string, array $subs = []) {
            return vsprintf($this->get($string), $subs);
        }

        /**
         * Replace curly quotes with uncurly quotes
         * @param $string
         * @return mixed
         */
        function uncurlQuotes($string) {
            $chr_map = array(
                // Windows codepage 1252
                "\xC2\x82" => "'", // U+0082⇒U+201A single low-9 quotation mark
                "\xC2\x84" => '"', // U+0084⇒U+201E double low-9 quotation mark
                "\xC2\x8B" => "'", // U+008B⇒U+2039 single left-pointing angle quotation mark
                "\xC2\x91" => "'", // U+0091⇒U+2018 left single quotation mark
                "\xC2\x92" => "'", // U+0092⇒U+2019 right single quotation mark
                "\xC2\x93" => '"', // U+0093⇒U+201C left double quotation mark
                "\xC2\x94" => '"', // U+0094⇒U+201D right double quotation mark
                "\xC2\x9B" => "'", // U+009B⇒U+203A single right-pointing angle quotation mark
                // Regular Unicode     // U+0022 quotation mark (")
                // U+0027 apostrophe     (')
                "\xC2\xAB" => '"', // U+00AB left-pointing double angle quotation mark
                "\xC2\xBB" => '"', // U+00BB right-pointing double angle quotation mark
                "\xE2\x80\x98" => "'", // U+2018 left single quotation mark
                "\xE2\x80\x99" => "'", // U+2019 right single quotation mark
                "\xE2\x80\x9A" => "'", // U+201A single low-9 quotation mark
                "\xE2\x80\x9B" => "'", // U+201B single high-reversed-9 quotation mark
                "\xE2\x80\x9C" => '"', // U+201C left double quotation mark
                "\xE2\x80\x9D" => '"', // U+201D right double quotation mark
                "\xE2\x80\x9E" => '"', // U+201E double low-9 quotation mark
                "\xE2\x80\x9F" => '"', // U+201F double high-reversed-9 quotation mark
                "\xE2\x80\xB9" => "'", // U+2039 single left-pointing angle quotation mark
                "\xE2\x80\xBA" => "'", // U+203A single right-pointing angle quotation mark
            );
            $chr = array_keys($chr_map); // but: for efficiency you should
            $rpl = array_values($chr_map); // pre-calculate these two arrays
            $string = str_replace($chr, $rpl, html_entity_decode($string, ENT_QUOTES, "UTF-8"));

            return $string;
        }

        /**
         * Detect current language from browser string.
         * 
         * TODO: Put more logic here, with better fallbacks.
         * @param bool $full if true, the full locale is returned, e.g. en_GB
         */
        public static function detectBrowserLanguage($full = true) { 
            
            $length = 2; // Short form
            if ($full)
                $length = 5;
            
            $lang = "";
            
            if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                $lang = preg_replace("/[^a-zA-Z\-_\s]/", "", substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, $length));
            }
            
            return $lang;
        }

    }

}