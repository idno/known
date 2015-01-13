<?php

namespace webignition\Url;

class Parser {
    
    const DEFAULT_PORT = 80;
    const MIN_PORT = 0;
    const MAX_PORT = 65535;
    
    const PROTOCOL_RELATIVE_START = '//';
   
    /**
     * Supplied URL, unmodified
     * 
     * @var string
     */
    private $origin = null; 
    
    
    /**
     * Origin URL that has been prepared to compensate for some missing
     * parts that parse_url() doesn't recognise
     * 
     * @var string
     */
    private $preparedOrigin = null;
    
    
    /**
     * Dummy scheme temporarily added to protocol-relative URLs to
     * allow parse_url() to correctly recognise subsequent parts
     * 
     * @var string
     */
    private $protocolRelativeDummyScheme = null;
    
    
    /**
     *
     * @var boolean
     */
    private $hasProtocolRelativeDummyScheme = false;
    
    
    /**
     * Collection of the different parts of the URL
     * 
     * @var array
     */
    private $parts = array();    
    
    public function __construct($url) {
        $this->origin = $url;
        $this->parse();        
    }
    
    /**
     *
     * @return array
     */
    public function getParts() {
        if (is_null($this->parts)) {
            $this->parse();
        }

        return $this->parts;
    }
    
        
    private function parse() {        
        $this->prepareOriginUrl();
        
        $this->parts = parse_url($this->preparedOrigin);
        
        if (isset($this->parts['query'])) {            
            $this->parts['query'] = new \webignition\Url\Query\Query($this->parts['query']);
        }
        
        if (isset($this->parts['path'])) {
            $this->parts['path'] = new \webignition\Url\Path\Path($this->parts['path']);                    
        }                
        
        if (isset($this->parts['host'])) {
            $this->parts['host'] = new \webignition\Url\Host\Host($this->parts['host']);                    
        }           
        
        if ($this->hasProtocolRelativeDummyScheme) {
            unset($this->parts['scheme']);
        }
        
        if (isset($this->parts['port'])) {
            $this->parts['port'] = (int)$this->parts['port'];
        }
    }
    
    private function prepareOriginUrl() {
        $this->preparedOrigin = $this->origin;
        $this->compensateForProtocolRelativeUrl();
    }
    
    
    /**
     * Check for (valid) lack of protocol as found in a protocol-relative URL
     * http://tools.ietf.org/html/rfc3986#page-26
     * 
     * Add in temporary protocol to allow parse_url() to correctly recognise
     * the supplied host and path
     * 
     */
    private function compensateForProtocolRelativeUrl() {
        if (substr($this->preparedOrigin, 0, strlen(self::PROTOCOL_RELATIVE_START)) == self::PROTOCOL_RELATIVE_START) {
            $this->preparedOrigin = $this->protocolRelativeDummyScheme() . ':' . $this->preparedOrigin;
            $this->hasProtocolRelativeDummyScheme = true;
            $this->isProtocolRelative = true;
        }
    }
    
    
    /**
     *
     * @return string
     */
    private function protocolRelativeDummyScheme() {
        if (is_null($this->protocolRelativeDummyScheme)) {
            $this->protocolRelativeDummyScheme = md5(microtime(true));
        }
        
        return $this->protocolRelativeDummyScheme;
    }
}