<?php

namespace webignition\NormalisedUrl\Path;

class Normaliser {
   
    /**
     * 
     * @var string
     */
    private $path = null;     
    
    /**
     *
     * @param string $path 
     */
    public function __construct($path) {
        $this->path = (string)$path;      
        $this->normalise();
    }    
    
    /**
     *
     * @return string
     */
    public function get() {
        return $this->path;
    }
    
    private function normalise() {
        $this->removeDotSegments();
        $this->reduceMultipleTrailingSlashes();
        $this->addTrailingSlash();
        $this->addLeadingSlash();        
    }
    
    /**
     * Directories are indicated with a trailing slash and should be included in URLs
     * Append trailing slash to path if not present 
     */    
    private function addTrailingSlash() {
        if (!isset($this->path)) {
            $this->path = '';
        }
        
        if ($this->path == '' || $this->path == '/') {
            return $this->path = '/';
        }
        

        
        $pathParts = explode('/', $this->path);
        
//        var_dump($pathParts, $this->path);
//        exit();
        
        return;        
        $lastPathPart = $pathParts[count($pathParts) - 1];

        if (substr_count($lastPathPart, '.')) {
            return;
        }
        
        $lastPathCharacter = substr($this->path, strlen($this->path) - 1);
        if ($lastPathCharacter != '/') {
            $this->path .= '/';
        }        
    }
    
    
    /**
     * Prepend path with leading slash if this URL has a host and the path lacks
     * the leading slash 
     */
    private function addLeadingSlash() {
        $firstPathCharacter = substr($this->path, 0, 1);
        
        if (isset($this->parts['host']) && $firstPathCharacter != '/') {
            $this->path = '/' . $this->path;
        }
    }
    
    
    /**
     * Remove the special "." and ".." complete path segments from a referenced path
     * 
     * Uses algorithm as defined in rfc3968#5.2.4
     * @see http://tools.ietf.org/html/rfc3986#section-5.2.4 
     */
    private function removeDotSegments() {        
        if ($this->path == '/') {
            return;
        }
        
        $dotOnlyPaths = array('/..', '/.');
        foreach ($dotOnlyPaths as $dotOnlyPath) {
            if ($this->path == $dotOnlyPath) {
                return $this->path = '/'; 
            }            
        }        
        
        $pathParts = explode('/', $this->path);        
        $comparisonPathParts = $pathParts;        
        
        $normalisedPathParts = array();
        
        foreach ($comparisonPathParts as $pathPart) {            
            if ($pathPart == '.') {
                continue;
            }
               
            if ($pathPart == '..') {
                array_pop($normalisedPathParts);
            } else {
                $normalisedPathParts[] = $pathPart;
            }
        }
        
        $this->path = implode('/', $normalisedPathParts);        
    }
    
    
    private function reduceMultipleTrailingSlashes() {
        if (!substr_count($this->path, '//')) {
            return;
        }
        
        $lastCharacter = substr($this->path, strlen($this->path) - 1);
        if ($lastCharacter != '/')  {
            return;
        }
        
        $trailingSlashSegmentIndex = strlen($this->path) - 1;
        $hasReachedNonSlashCharacter = false;
        
        //var_dump($this->path);
        
        for ($characterIndex = strlen($this->path) - 1; $characterIndex >= 0; $characterIndex--) {
            if (!$hasReachedNonSlashCharacter) {
                //var_dump($characterIndex);
                if ($this->path[$characterIndex] == '/') {
                    //var_dump("beep $characterIndex");
                    $trailingSlashSegmentIndex = $characterIndex;
                } else {
                    $hasReachedNonSlashCharacter = true;
                }                
            }
        }
        
//        var_dump($trailingSlashSegmentIndex);
//        exit();
//        
        $trailingSlashSegment = ($trailingSlashSegmentIndex == 0) ? $this->path : substr($this->path, $trailingSlashSegmentIndex);
        
        while (substr_count($trailingSlashSegment, '//')) {
            $trailingSlashSegment = str_replace('//', '/', $trailingSlashSegment);
        }
        
        $this->path = substr($this->path, 0, $trailingSlashSegmentIndex) . $trailingSlashSegment;
        
        return;
        
        var_dump($trailingSlashSegment);
        
        exit();
        
        
        $currentPathParts = explode('/', $this->path);
        $reversePathParts = array();
        $hasFoundNonBlankPart = false;
        
        var_dump($currentPathParts);
//        /exit();
        
        for ($partIndex = count($currentPathParts) - 1; $partIndex >= 0; $partIndex--) {            
            $part = trim($currentPathParts[$partIndex]);
            
            if ($part != '') {
                $hasFoundNonBlankPart = true;
            }
            
            if ($hasFoundNonBlankPart || (!$hasFoundNonBlankPart && $part != '')) {
                $reversePathParts[] = $part;
            }
        }
        
        var_dump(array_reverse($reversePathParts));
        exit();
        
        $this->path = implode('/', array_reverse($reversePathParts)) ;
    }
    
}