<?php
class MentionClient {

  public $debug = false;

  private $_sourceURL;
  private $_sourceBody;

  private $_links = array();

  private $_headers = array();
  private $_body = array();
  private $_supportsPingback = array();
  private $_supportsWebmention = array();
  private $_pingbackServer = array();
  private $_webmentionServer = array();

  public function __construct($sourceURL, $sourceBody=false) {
    $this->_sourceURL = $sourceURL;
    if($sourceBody)
      $this->_sourceBody = $sourceBody;
    else
      $this->_sourceBody = $this->_get($sourceURL);

    // Find all external links in the source
    preg_match_all("/<a[^>]+href=.(https?:\/\/[^'\"]+)/i", $this->_sourceBody, $matches);
    $this->_links = array_unique($matches[1]);
  }

  public function supportsPingback($target) {

    if($this->c('supportsPingback', $target) === null) {
      $this->c('supportsPingback', $target, false);

      // First try a HEAD request and look for X-Pingback header 
      if(!$this->c('headers', $target)) {
        $this->c('headers', $target, $this->_fetchHead($target));
      }

      $headers = $this->c('headers', $target);
      if(array_key_exists('X-Pingback', $headers)) {
        $this->_debug("Found pingback server in header");
        $this->c('pingbackServer', $target, $headers['X-Pingback']);
        $this->c('supportsPingback', $target, true);
      } else {
        $this->_debug("No pingback server found in header, looking in the body now");
        if(!$this->c('body', $target)) {
          $this->c('body', $target, $this->_fetchBody($target));
        }
        $body = $this->c('body', $target);
        if(preg_match("/<link rel=\"pingback\" href=\"([^\"]+)\" ?\/?>/i", $body, $match)) {
          $this->c('pingbackServer', $target, $match[1]);
          $this->c('supportsPingback', $target, true);
        }
      }

      $this->_debug("pingback server: " . $this->c('pingbackServer', $target));
    }

    return $this->c('supportsPingback', $target);
  }

  public function supportsWebmention($target) {

    if($this->c('supportsWebmention', $target) === null) {
      $this->c('supportsWebmention', $target, false);

      // First try a HEAD request and look for Link header 
      if(!$this->c('headers', $target)) {
        $this->c('headers', $target, $this->_fetchHead($target));
      }

      $headers = $this->c('headers', $target);
      if(array_key_exists('Link', $headers) && preg_match('~<(https?://[^>]+)>; rel="http://webmention.org/"~', $headers['Link'], $match)) {
        $this->_debug("Found webmention server in header");
        $this->c('webmentionServer', $target, $match[1]);
        $this->c('supportsWebmention', $target, true);
      } else {
        $this->_debug("No webmention server found in header, looking in the body now");
        if(!$this->c('body', $target)) {
          $this->c('body', $target, $this->_fetchBody($target));
        }
        if(preg_match('/<link href="([^"]+)" rel="http:\/\/webmention.org\/" ?\/?>/i', $this->c('body', $target), $match)) {
          $this->c('webmentionServer', $target, $match[1]);
          $this->c('supportsWebmention', $target, true);
        }
      }

      $this->_debug("webmention server: " . $this->c('webmentionServer', $target));
    }

    return $this->c('supportsWebmention', $target);
  }

  public function sendSupportedMentions($target=false) {

    if($target == false) {
      $totalSent = 0;

      foreach($this->_links as $link) {
        $this->_debug("Checking $link");
        $totalSent += $this->sendSupportedMentions($link);
        $this->_debug('');
      }

      return $totalSent;
    }

    $sent = false;

    // Try pingback first since it will be more common for now. Eventually will probably switch this.
    if($this->supportsPingback($target)) {
      $this->_debug("Sending pingback now!");

      $pingbackServer = $this->c('pingbackServer', $target);
      $this->_debug("Sending to pingback server: " . $pingbackServer);

      $payload = xmlrpc_encode_request('pingback.ping', array($this->_sourceURL,  $target));

      $response = $this->_post($pingbackServer, $payload, array(
        'Content-type: application/xml'
      ));
      $this->_debug($response);

      $sent = true;
    }

    // Only send a webmention if we didn't find a pingback server
    if($sent == false && $this->supportsWebmention($target)) {
      $this->_debug("Sending webmention now!");

      $webmentionServer = $this->c('webmentionServer', $target);
      $this->_debug("Sending to webmention server: " . $webmentionServer);

      $payload = http_build_query(array(
        'source' => $this->_sourceURL,
        'target' => $target
      ));

      $response = $this->_post($webmentionServer, $payload, array(
        'Content-type: application/x-www-url-form-encoded',
        'Accept: application/json'
      ));
      $this->_debug($response);

      $sent = true;
    }

    if($sent) 
      return 1;
    else
      return 0;
  }

  private function _debug($msg) {
    if($this->debug)
      echo "\t" . $msg . "\n";
  }

  private function _fetchHead($url) {
    $this->_debug("Fetching headers...");
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    $response = curl_exec($ch);
    return $this->_parse_headers($response);
  }

  private function _fetchBody($url) {
    $this->_debug("Fetching body...");
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    return curl_exec($ch);
  }

  private function _parse_headers($headers) {
    $retVal = array();
    $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $headers));
    foreach($fields as $field) {
      if(preg_match('/([^:]+): (.+)/m', $field, $match)) {
        $match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(trim($match[1])));
        if(isset($retVal[$match[1]])) {
          $retVal[$match[1]] = array($retVal[$match[1]], $match[2]);
        } else {
          $retVal[$match[1]] = trim($match[2]);
        }
      }
    }
    return $retVal;
  }

  private function _get($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    return curl_exec($ch);
  }

  private function _post($url, $body, $headers=array()) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    return curl_exec($ch);
  }

  private function c($type, $url, $val=null) {
    // Create the empty record if it doesn't yet exist
    $key = '_'.$type;

    if(!array_key_exists($url, $this->{$key})) {
      $this->{$key}[$url] = null;
    }

    if($val !== null) {
      $this->{$key}[$url] = $val;
    }

    return $this->{$key}[$url];
  }

}