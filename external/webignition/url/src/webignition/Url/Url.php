<?php
namespace webignition\Url;

class Url {
    
    /**
     *
     * @var \webignition\Url\Parser
     */
    private $parser = null;    
    
    /**
     * Original unmodified source URL
     * 
     * @var string
     */
    private $originUrl = '';
    
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
     * @var array
     */
    private $offsets = null;
    
    /**
     *
     * @var array
     */
    private $availablePartNames = array(
        'scheme',
        'user',
        'pass',
        'host',
        'port',        
        'path',
        'query',
        'fragment'
    );    
    
    
    /**
     *
     * @param string $originUrl 
     */
    public function __construct($originUrl) {
        $this->originUrl = $originUrl;
    } 
    
    
    /**
     *
     * @return string 
     */
    public function getRoot() {
        $rawRootUrl = '';
        
        if ($this->hasScheme()) {
            $rawRootUrl .= $this->getScheme() . ':';
        }
        
        if ($this->hasHost()) {
            $rawRootUrl .= '//';
            
            if ($this->hasCredentials()) {
                $rawRootUrl .= $this->getCredentials() . '@';
            }            
            
            $rawRootUrl .= $this->getHost();
        }        
        
        if ($this->hasPort()) {
            $rawRootUrl .= ':' . $this->getPort();
        }
        
        return $rawRootUrl;
    }
    
    
    /**
     *
     * @return array
     */
    protected function &parts() {
        if (is_null($this->parts)) {
            $this->parts = $this->parser()->getParts();
        }
        
        return $this->parts;
    }
    
    /**
     *
     * @return boolean
     */
    public function hasScheme() {
        return $this->hasPart('scheme');
    }
    
    
    /**
     *
     * @return string
     */
    public function getScheme() {
        return $this->getPart('scheme');
    }
    
    
    /**
     *
     * @param string $scheme 
     */
    public function setScheme($scheme) {
        $this->setPart('scheme', $scheme);
    }

    
    /**
     *
     * @return boolean
     */    
    public function hasHost() {
        return $this->hasPart('host');
    }
    
    
    /**
     *
     * @return \webignition\Url\Host\Host
     */
    public function getHost() {
        return $this->getPart('host');
    }
    
    /**
     *
     * @param string $host 
     */
    public function setHost($host) {
        $this->setPart('host', $host);
    }
    
    
    /**
     *
     * @return boolean
     */    
    public function hasPort() {
        return $this->hasPart('port');
    }
    
    
    /**
     *
     * @return int
     */
    public function getPort() {
        return $this->getPart('port');
    }
    
    
    /**
     *
     * @param int $port 
     */
    public function setPort($port) {
        $this->setPart('port', $port);
    }
    
    
    /**
     *
     * @return boolean
     */    
    public function hasUser() {
        return $this->hasPart('user');
    }
    
    
    /**
     *
     * @return string
     */
    public function getUser() {
        return $this->getPart('user');
    }
    
    
    /**
     *
     * @param string $user 
     */
    public function setUser($user) {
        $this->setPart('user', $user);
    }
    
    
    /**
     *
     * @return boolean
     */    
    public function hasPass() {
        return $this->hasPart('pass');
    }
    
    
    /**
     *
     * @return string
     */
    public function getPass() {
        return $this->getPart('pass');
    }
    
    
    /**
     *
     * @param string $pass 
     */
    public function setPass($pass) {
        $this->setPart('pass', $pass);
    }
    
    
    /**
     *
     * @return boolean
     */    
    public function hasPath() {
        return $this->hasPart('path');
    }
    
    
    /**
     *
     * @return \webignition\Url\Path\Path
     */
    public function getPath() {        
        return $this->getPart('path');
    }
    
    
    /**
     *
     * @param string $path 
     */
    public function setPath($path) {        
        $this->setPart('path', $path);
    }
    
    
    /**
     *
     * @return boolean
     */    
    public function hasQuery() {
        return $this->hasPart('query');
    }
    
    
    /**
     *
     * @return \webignition\Url\Query
     */
    public function getQuery() {        
        return $this->getPart('query');
    }
    
    
    /**
     *
     * @param string $query 
     */
    public function setQuery($query) {
        $this->setPart('query', $query);
    }
    
    
    /**
     *
     * @return boolean
     */    
    public function hasFragment() {
        return $this->hasPart('fragment');
    }
    
    
    /**
     *
     * @return string
     */
    public function getFragment() {
        return $this->getPart('fragment');
    }
    
    
    /**
     *
     * @param string $fragment
     */
    public function setFragment($fragment) {
        $this->setPart('fragment', $fragment);
    }    

    
    /**
     *
     * @return string 
     */
    public function __toString() {
        $url = $this->getRoot();
        
        $url .= $this->getPath();
        
        if ($this->hasQuery()) {
            $url .= '?' . $this->getQuery();
        }
        
        if ($this->hasFragment()) {
            $url .= '#' . $this->getFragment();
        }
        
        return $url;        
    }
    
    
    /**
     *
     * @return boolean 
     */
    public function isRelative() {
        if ($this->hasScheme()) {
            return false;
        }
        
        if ($this->hasHost()) {
            return false;
        }
        
        return true;
    }
    
    
    /**
     *
     * @return boolean 
     */
    public function isProtocolRelative() {
        if ($this->hasScheme()) {
            return false;
        }
        
        return $this->hasHost();
    }
    

    /**
     *
     * @return boolean
     */
    public function isAbsolute() {
        if ($this->isRelative()) {
            return false;
        }
        
        return !$this->isProtocolRelative();
    }
    
    
    /**
     *
     * @param string $partName
     * @param string $value 
     */
    private function setPart($partName, $value) {
        if ($this->hasPart($partName)) {
            $this->replacePart($partName, $value);
        } else {
            $this->addPart($partName, $value);
        }

        $this->reset();
    }
    
    
    /**
     * 
     * @param string $partName
     * @param string $value 
     */
    private function replacePart($partName, $value) {        
        if ($partName == 'query' && substr($value, 0, 1) == '?') {
            $value = substr($value, 1);
        }
        
        if ($partName == 'fragment' && substr($value, 0, 1) == '#') {
            $value = substr($value, 1);
        }
        
        $offsets = &$this->offsets();        
        $this->originUrl = substr_replace($this->originUrl, $value, $offsets[$partName], strlen($this->getPart($partName)));  
    }
    
    
    private function addPart($partName, $value) {
        if ($partName == 'scheme') {
            return $this->addScheme($value);
        }
        
        if ($partName == 'user') {
            return $this->addUser($value);
        }
        
        if ($partName == 'host') {
            return $this->addHost($value);
        }        
        
        if ($partName == 'pass') {
            return $this->addPass($value);
        }
        
        if ($partName == 'query') {
            return $this->addQuery($value);
        }
        
        if ($partName == 'fragment') {
            return $this->addFragment($value);
        }
        
        if ($partName == 'path') {
            return $this->addPath($value);
        }
    }
    
    
    /**
     * Add a scheme to a URL that does not already have one
     * 
     * @param string $scheme
     * @return boolean 
     */
    private function addScheme($scheme) {
        if ($this->hasScheme()) {
            return false;
        }
        
        if (!$this->isProtocolRelative()) {
            $this->originUrl = '//' . $this->originUrl;
        }
        
        if (substr($this->originUrl, 0, 1) != ':') {
            $this->originUrl = ':' . $this->originUrl;
        }
        
        $this->originUrl = $scheme . $this->originUrl;
    }
    
    
    /**
     * Add a user to a URL that does not already have one
     * 
     * @param string $user
     * @return boolean 
     */
    private function addUser($user) {
        if ($this->hasUser()) {
            return false;
        }
        
        if (!is_string($user)) {
            $user = '';
        }
        
        $user = trim($user);
        if ($user == '') {
            return true;
        }
        
        // A user cannot be added to a URL that has no host; this results in
        // an invalid URL.
        if (!$this->hasHost()) {
            return false;
        }
        
        $nextPartName = $this->getNextPartName('user');
        $offsets = &$this->offsets();
        
        if ($nextPartName == 'host') {
            $preNewPart = substr($this->originUrl, 0, $offsets[$nextPartName]);            
            $postNewPart = substr($this->originUrl, $offsets[$nextPartName]);              
            
            return $this->originUrl = $preNewPart . $user . ':@' . $postNewPart;
        }

        $preNewPart = substr($this->originUrl, 0, $offsets[$nextPartName] - 1);
        $postNewPart = substr($this->originUrl, $offsets[$nextPartName] - 1);

        return $this->originUrl = $preNewPart . $user . $postNewPart;
    }
    
    
    private function addPass($pass) {       
        if ($this->hasPass()) {
            return false;
        }       
        
        // A pass cannot be added to a URL that has no host; this results in
        // an invalid URL.
        if (!$this->hasHost()) {
            return false;
        }
        
        $offsets = &$this->offsets();
        
        if ($this->hasUser()) {
            $preNewPart = substr($this->originUrl, 0, $offsets['host'] - 1);
            $postNewPart = substr($this->originUrl, $offsets['host'] - 1);

            return $this->originUrl = $preNewPart . $pass . $postNewPart;
        }
        
        $preNewPart = substr($this->originUrl, 0, $offsets['host']);
        $postNewPart = substr($this->originUrl, $offsets['host']);

        return $this->originUrl = $preNewPart . ':' . $pass . '@' . $postNewPart;        
    }
    
    
    /**
     * Add a host to a URL that does not already have one
     * 
     * @param string $host
     * @return boolean 
     */
    private function addHost($host) {        
        if ($this->hasHost()) {
            return false;
        }
 
        if ($this->hasPath() && $this->getPath()->isRelative()) {
            $this->setPath('/' . $this->getPath());
        }
        
        return $this->originUrl = '//' . $host . $this->originUrl;        
    }
    
    
    /**
     * Add query to a URL that does not already have one
     * 
     * @param string $query
     * @return boolean 
     */
    private function addQuery($query) {
        if ($this->hasQuery()) {
            return false;
        }
        
        if (is_null($query)) {
            return true;
        }
        
        if (substr($query, 0, 1) != '?') {
            $query = '?' . $query;
        }
        
        if ($this->hasFragment()) {
            $offsets = &$this->offsets();
            $preNewPart = substr($this->originUrl, 0, $offsets['fragment'] - 1);
            $postNewPart = substr($this->originUrl, $offsets['fragment'] - 1);

            return $this->originUrl = $preNewPart . $query . $postNewPart;          
        }
        
        return $this->originUrl .= $query;
    }
    
    
    /**
     * Add a fragment to a URL that does not already have one
     * 
     * @param string $fragment
     * @return boolean 
     */
    public function addFragment($fragment) {
        if ($this->hasFragment()) {
            return false;
        }
        
        if (!is_string($fragment)) {
            $fragment = '';
        }
        
        $fragment = trim($fragment);
        
        if ($fragment == '') {
            return true;
        }

        if (substr($fragment, 0, 1) != '#') {
            $fragment = '#' . $fragment;
        }
        
        return $this->originUrl .= $fragment;
    }
    
    
    /**
     *  Add a path to a URL that does not already have one
     * 
     * @param string $path
     * @return boolean 
     */
    public function addPath($path) {       
        if ($this->hasPath()) {
            return false;
        }
        
        if (!$this->hasPart('query') && !$this->hasPart('fragment')) {
            return $this->originUrl = $this->originUrl . $path;
        }
        
        $nextPartName = $this->getNextPartName('path');
        $offsets = &$this->offsets();
        
        $offset = $offsets[$nextPartName];
        
        if ($nextPartName == 'fragment') {
            $offset -= 1;
        }
        
        return $this->originUrl = substr($this->originUrl, 0, $offset) . $path . substr($this->originUrl, $offset);        
    }
    
    
    /**
     * Get the next url part after $partName that is present in this
     * url
     * 
     * @param string $partName
     * @return string
     */
    private function getNextPartName($partName) {        
        $hasFoundPart = false;        
        foreach ($this->availablePartNames as $availablePartName) {
            if ($partName == $availablePartName) {
                $hasFoundPart = true;
            }
            
            if ($hasFoundPart === false) {
                continue;
            }
            
            if ($availablePartName != $partName && $this->hasPart($availablePartName)) {
                return $availablePartName;
            }
        }
        
        return null;
    }
    
    
    private function &offsets() {        
        if (is_null($this->offsets)) {
            $this->offsets = array();
            
            $partNames = array();
            foreach ($this->availablePartNames as $availablePartName) {
                if ($this->hasPart($availablePartName)) {
                    $partNames[] = $availablePartName;
                }
            }

            $originUrlComparison = str_split(urldecode($this->originUrl));
            $originUrlLength = strlen($this->originUrl);

            foreach ($partNames as $partName) {                
                $currentPartMatch = '';
                $currentPart = urldecode((string)$this->parts[$partName]);
                $currentPartFirstCharacter = substr($currentPart, 0, 1);                

                while ($currentPartMatch != $currentPart) {
                    $nextCharacter = array_shift($originUrlComparison);

                    if ($currentPartMatch == '' && $nextCharacter != $currentPartFirstCharacter) {
                        continue;
                    }
                    
                    if ($currentPartMatch == '') {
                        $this->offsets[$partName] = $originUrlLength - count($originUrlComparison) - 1;
                    }

                    $currentPartMatch .= $nextCharacter;
                }
            }
        }
        
        return $this->offsets;
    }
    
    
    protected function reset() {
        $this->parser = null;
        $this->parts = $this->parser()->getParts();
        $this->offsets = null;
    }
    
    
    /**
     *
     * @return boolean
     */
    private function hasCredentials() {
        return $this->hasUser() || $this->hasPass();
    }
    
    
    /**
     *
     * @return string 
     */
    private function getCredentials() {
        $credentials = '';
        
        if ($this->hasUser()) {
            $credentials .= $this->getUser();
        }
        
        $credentials .= ':';
        
        if ($this->hasPass()) {
            $credentials .= $this->getPass();
        }
        
        return $credentials;
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
     * @param string $partName
     * @return boolean
     */
    protected function hasPart($partName) {
        if (is_null($this->getPart($partName))) {
            return false;
        }
        
        return isset($this->parts[$partName]);
    }

    
    /**
     *
     * @return \webignition\Url\Parser
     */
    private function parser() {
        if (is_null($this->parser)) {
            $this->parser = new \webignition\Url\Parser($this->originUrl);
        }
        
        return $this->parser;
    }
}