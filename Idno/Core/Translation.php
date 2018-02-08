<?php


namespace Idno\Core {
    
    use Idno\Common\Component;
    
    /**
     * Register translation strings for a specific language.
     */
    abstract class Translation 
        extends Component 
    {
        
        /**
         * Language this translation is for.
         * @var type 
         */
        protected $language;
        
        /**
         * Create this translation, for the defined language.
         * @param type $language Which language is this for? Default 'en'
         */
        public function __construct($language = 'en') {
            $this->language = $language;
        }
        
        public function getLanguage() {
            return $this->language;
        }
        
        /**
         * Return a specific string.
         */
        abstract public function getString($key);
        
    }
    
}