<?php
namespace webignition\NormalisedUrl\Path;

/**
 * Represents the normalised path part of a URL
 *  
 */
class Path extends \webignition\Url\Path\Path {
        
    /**
     *
     * @var \webignition\NormalisedUrl\Path\Normaliser
     */
    private $normaliser = null;
    
    
    /**
     *
     * @param string $path 
     */
    public function __construct($path) {        
        $this->set($this->normaliser($path)->get());
    }
    
    /**
     *
     * @param string $path
     * @return \webignition\NormalisedUrl\Path\Normaliser 
     */
    private function normaliser($path) {
        if (is_null($this->normaliser)) {
            $this->normaliser = new \webignition\NormalisedUrl\Path\Normaliser($path);
        }
        
        return $this->normaliser;
    }
}