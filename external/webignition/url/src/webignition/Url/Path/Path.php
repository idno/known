<?php
namespace webignition\Url\Path;

/**
 * Represents the path part of a URL
 *  
 */
class Path {
    
    const HOST_PATH_SEPARATOR = '/';
    
    /**
     * 
     * @var string
     */
    private $path = '';    
    
    
    /**
     *
     * @param string $path 
     */
    public function __construct($path) {
        $this->set($path);
    }
    
    /**
     *
     * @return boolean
     */
    public function isRelative() {
        return !$this->isAbsolute();
    }
    
    /**
     *
     * @return boolean
     */
    public function isAbsolute() {
        return substr($this->path, 0, 1) === self::HOST_PATH_SEPARATOR;
    }
    
    
    /**
     *
     * @return string
     */
    public function get() {
        return $this->path;
    }
    
    
    /**
     *
     * @param string $path 
     */
    public function set($path) {
        $this->path = trim($path);
    }
    
    /**
     *
     * @return string 
     */
    public function __toString() {              
        return $this->get();       
    }
    
    
    /**
     *
     * @return boolean
     */
    public function hasFilename() {        
        if (substr($this->path, strlen($this->path) - 1) == '/') {
            return false;
        }
        
        return substr_count(basename($this->path), '.') > 0;
    }
    
    /**
     *
     * @return string
     */
    public function getFilename() {
        return $this->hasFilename() ? basename($this->path) : '';
    }
    
    /**
     *
     * @return string
     */
    public function getDirectory() {
        return $this->hasFilename() ? dirname($this->path) : $this->path;
    }
    
    
    public function hasTrailingSlash() {
        return substr($this->get(), strlen($this->get()) - 1) == '/';
    }
}