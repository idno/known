<?php
namespace webignition\NormalisedUrl;

class NormalisedUrl extends \webignition\Url\Url {
    
    
    /**
     *
     * @var \webignition\NormlisedUrl\Normaliser
     */
    private $normaliser = null;
    
    /**
     * Component parts of this URL, with keys:
     * -scheme
     * -host
     * -port
     * -user
     * -pass
     * -path
     * -query - after the question mark ?
     * -fragment - after the hashmark #
     * 
     * @var array
     */
    private $parts = null;    
    
    /**
     *
     * @param string $originUrl 
     */
    public function __construct($originUrl) {
        parent::__construct($originUrl);
    } 
    
    
    /**
     *
     * @return array
     */
    protected function &parts() {
        if (is_null($this->parts)) {
            $this->parts = $this->normaliser()->getNormalisedParts();
        }
        
        return $this->parts;
    }

    
    protected function reset() {
        parent::reset();
        $this->parts = null;
        $this->normaliser = null;
    }
   
    /**
     *
     * @param string $partName
     * @return mixed
     */
    protected function getPart($partName) {
        $parts = &$this->parts(); 
        return (isset($parts[$partName])) ? $parts[$partName] : null;
    }
    
    /**
     *
     * @return \webignition\NormalisedUrl\Normaliser
     */
    private function normaliser() {
        if (is_null($this->normaliser)) {
            $this->normaliser = new \webignition\NormalisedUrl\Normaliser(parent::parts());
        }
        
        return $this->normaliser;
    }
}