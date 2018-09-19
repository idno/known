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
         * @param type $language Which language is this for? Default 'en_US'
         */
        public function __construct($language = 'en_US') {
            $this->language = $language;
        }
        
        /**
         * Can this object provide the given language.
         * @param type $language
         * @return bool
         */
        public function canProvide($language) {
            return $this->language == $language;
        }
        
        /**
         * Return a specific string.
         */
        abstract public function getString($key);
        
    }
    
}
