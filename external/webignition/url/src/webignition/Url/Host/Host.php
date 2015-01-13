<?php
namespace webignition\Url\Host;

/**
 * Represents the host part of a URL
 *  
 */
class Host {
    
    const HOST_PART_SEPARATOR = '.';
    
    /**
     * 
     * @var string
     */
    private $host = '';    
    
    
    /**
     *
     * @var array
     */
    private $parts = null;
    
    
    /**
     *
     * @param string $host 
     */
    public function __construct($host) {
        $this->set($host);
    }
    
    
    /**
     *
     * @return string
     */
    public function get() {
        return $this->host;
    }
    
    
    /**
     *
     * @param string $host 
     */
    public function set($host) {
        $this->host = trim($host);
        $this->parts = null;
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
     * @return array
     */
    public function getParts() {
        if (is_null($this->parts)) {
            $this->parts = explode(self::HOST_PART_SEPARATOR, $this->get());
        }
        
        return $this->parts;
    }
    
    
    /**
     * 
     * @param \webignition\Url\Host\Host $comparator
     * @return boolean
     */
    public function equals(Host $comparator) {
        return $this->get() == $comparator->get();
    }
    
    
    /**
     * 
     * @param \webignition\Url\Host\Host $comparator
     * @param array $excludeParts
     * @return boolean
     */
    public function isEquivalentTo(Host $comparator, $excludeParts = array()) {
        $thisHost = new Host(idn_to_ascii((string)$this));
        $comparatorHost = new Host(idn_to_ascii((string)($comparator)));
        
        if (!is_array($excludeParts) || count($excludeParts) == 0) {
            return $thisHost->equals($comparatorHost);
        }
        
        $thisParts = $this->excludeParts($thisHost->getParts(), $excludeParts);
        $comparatorParts = $this->excludeParts($comparatorHost->getParts(), $excludeParts);
        
       return $thisParts == $comparatorParts;
    }
    
    
    /**
     * 
     * @param array $parts
     * @param array $exclusions
     * @return array
     */
    private function excludeParts($parts, $exclusions) {
        $filteredParts = array();
        
        foreach ($parts as $index => $part) {
            if (!isset($exclusions[$index]) || $exclusions[$index] != $part) {
                $filteredParts[] = $part;
            }
        }
        
        return $filteredParts;
    }
    
}