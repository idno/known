<?php

namespace webignition\AbsoluteUrlDeriver;

/**
 * 
 * @package webignition\AbsoluteUrlDeriver
 *
 */
class AbsoluteUrlDeriver {
    
    /**
     *
     * @var \webignition\NormalisedUrl\NormalisedUrl 
     */
    private $nonAbsoluteUrl = null;
    
    
    /**
     *
     * @var \webignition\NormalisedUrl\NormalisedUrl 
     */
    private $sourceUrl = null;
    
    
    /**
     *
     * @var \webignition\NormalisedUrl\NormalisedUrl 
     */
    private $absoluteUrl = null;
    
    
    /**
     *
     * @param string $nonAbsoluteUrl
     * @param string $sourceUrl 
     */
    public function __construct($nonAbsoluteUrl, $sourceUrl) {        
        $this->nonAbsoluteUrl = new \webignition\Url\Url($nonAbsoluteUrl);
        $this->sourceUrl = new \webignition\NormalisedUrl\NormalisedUrl($sourceUrl);        
    }
    
    
    public function getAbsoluteUrl() {
        if (is_null($this->absoluteUrl)) {
            $this->deriveAbsoluteUrl();
        }
        
        return $this->absoluteUrl;
    }
    
    
    private function deriveAbsoluteUrl() {
        $this->absoluteUrl = clone $this->nonAbsoluteUrl;
        
        if (!$this->absoluteUrl->isAbsolute()) {        
            $this->derivePath();        
            $this->deriveHost();
            $this->deriveScheme();

            $this->deriveUser();
            $this->derivePass();
        }        
    }
    
    private function deriveHost() {
        if (!$this->absoluteUrl->hasHost()) {
            if ($this->sourceUrl->hasHost()) {
                $this->absoluteUrl->setHost($this->sourceUrl->getHost());
            }
        }        
    }
    
    private function deriveScheme() {
        if (!$this->absoluteUrl->hasScheme()) {
            if ($this->sourceUrl->hasScheme()) {
                $this->absoluteUrl->setScheme($this->sourceUrl->getScheme());
            }
        }
    }   
    
    private function derivePath() {                
        if ($this->absoluteUrl->hasPath() && $this->absoluteUrl->getPath()->isRelative()) {
            if ($this->sourceUrl->hasPath()) {
                /* @var $pathDirectory \webignition\NormalisedUrl\Path\Path */
                $rawPathDirectory = $this->sourceUrl->getPath()->hasFilename() ? dirname($this->sourceUrl->getPath()) : (string)$this->sourceUrl->getPath();
                
                $pathDirectory = new \webignition\NormalisedUrl\Path\Path($rawPathDirectory);                                  
                $derivedPath = $pathDirectory;
                
                if (!$pathDirectory->hasTrailingSlash()) {
                    $derivedPath .= '/';
                }
                
                $derivedPath .= $this->absoluteUrl->getPath();                
                $normalisedDerivedPath = new \webignition\NormalisedUrl\Path\Path((string)$derivedPath);                  
                $this->absoluteUrl->setPath($normalisedDerivedPath);
            }
        }
        
        if (!$this->absoluteUrl->hasPath()) {
            if ($this->sourceUrl->hasPath()) {                
                $this->absoluteUrl->setPath($this->sourceUrl->getPath());
            }
        }       
    }
    
    private function deriveUser() {
        if (!$this->absoluteUrl->hasUser() && $this->sourceUrl->hasUser()) {
            $this->absoluteUrl->setUser($this->sourceUrl->getUser());
        }
    }
    
    
    private function derivePass() {
        if (!$this->absoluteUrl->hasPass() && $this->sourceUrl->hasPass()) {
            $this->absoluteUrl->setPass($this->sourceUrl->getPass());
        }
    }
    
    
    
//    /**
//     *
//     * @return string
//     */
//    public function getUrl() {
//        $url = $this->getScheme().'://'.$this->getCredentialsString().$this->getHost();
//        
//        if (!$this->hostEndsWithPathPartSeparator() && !$this->pathStartsWithPathPartSeparator()) {
//            $url .= '/';
//        }        
//        
//        $url .= $this->getPath().$this->getQueryString();
//        
//        return $url;
//    }    
//
//    
//    /**
//     *
//     * @return string
//     */    
//    public function getScheme() {
//        return (parent::getScheme() == '') ? $this->sourceUrl->getScheme() : parent::getScheme();
//    }
//    
//    /**
//     *
//     * @return string
//     */    
//    public function getHost() {
//        return (parent::getHost() == '') ? $this->sourceUrl->getHost() : parent::getHost();
//    }
//    
//    /**
//     *
//     * @return string
//     */    
//    public function getUsername() {
//        return (parent::getUsername() == '') ? $this->sourceUrl->getUsername() : parent::getUsername();
//    }
//    
//    /**
//     *
//     * @return string
//     */    
//    public function getPassword() {
//        return (parent::getPassword() == '') ? $this->sourceUrl->getPassword() : parent::getPassword();
//    }
//    
//    /**
//     *
//     * @return string
//     */    
//    public function getPath() {                
//        if ($this->parentPathStartsWithPathPartSeparator()) {
//            return (parent::getPath() == '') ? $this->sourceUrl->getPath() : parent::getPath();
//        }
//  
//        return ($this->sourceUrl->pathEndsWithPathPartSeparator()) ?
//            $this->sourceUrl->getPath() . parent::getPath():
//            $this->sourceUrl->getPath() . '/' . parent::getPath();        
//    }
//    
//    /**
//     *
//     * @return string
//     */    
//    public function getFragment() {
//        return (parent::getFragment() == '') ? $this->sourceUrl->getFragment() : parent::getFragment();
//    }
//    
//    /**
//     *
//     * @return boolean
//     */
//    protected function parentPathStartsWithPathPartSeparator() {
//        return substr(parent::getPath(), 0, 1) == self::PATH_PART_SEPARATOR;
//    }
}