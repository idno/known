<?php

namespace webignition\NormalisedUrl;

class Normaliser {
    
    const DEFAULT_PORT = 80;
    
    /**
     * Collection of the different parts of the URL
     * 
     * @var array
     */
    private $parts = array();
    
    
    /**
     *
     * @param array $parts 
     */
    public function __construct($parts) {
        $this->parts = $parts;
        $this->normalise();        
    }
    
    
    /**
     *
     * @return array
     */
    public function getNormalisedParts() {        
        return $this->parts;
    }
    
    private function normalise() {
        $this->normaliseScheme();
        $this->normaliseHost();
        $this->normalisePort();
        $this->normalisePath();
        $this->normaliseQuery();
    }
    
    
    /**
     * Scheme is case-insensitive, normalise to lowercase 
     */
    private function normaliseScheme() {
        if (isset($this->parts['scheme'])) {
            $this->parts['scheme'] = strtolower(trim($this->parts['scheme']));
        }
    }
    
    
    /**
     * Host is case-insensitive, normalise to lowercase and to ascii version of
     * IDN format
     */
    private function normaliseHost() {
        if (isset($this->parts['host'])) {
            $host = idn_to_ascii($this->parts['host']->get());
            $host = trim($host);
            $host = strtolower($host);
            
            $this->parts['host']->set($host);
        }
    }
    
    
    /**
     * Remove default HTTP port 
     */
    private function normalisePort() {
        if (isset($this->parts['port']) && $this->parts['port'] == self::DEFAULT_PORT) {
            unset($this->parts['port']);
        }
    }    
    
    private function normalisePath() {
        if (!isset($this->parts['path'])) {
            $this->parts['path'] = '';
        }
        
        $this->parts['path'] = new \webignition\NormalisedUrl\Path\Path((string)$this->parts['path']);
    }    
    
    private function normaliseQuery() {
        if (isset($this->parts['query'])) {
            $this->parts['query'] = new \webignition\NormalisedUrl\Query\Query((string)$this->parts['query']);
        }
    }
    
}