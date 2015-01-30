<?php
namespace IndieWeb;

class MentionClient {

  private $_debugging = false;
  private static $_debugStatic = false;

  private $_sourceURL;
  private $_sourceBody;

  private $_links = array();

  private $_headers = array();
  private $_body = array();
  private $_supportsPingback = array();
  private $_supportsWebmention = array();
  private $_pingbackServer = array();
  private $_webmentionServer = array();

  private $_proxy = false;
  private static $_proxyStatic = false;
  
  public function __construct($sourceURL, $sourceBody=false, $proxyString=false) {
    $this->setProxy($proxyString);
    $this->_sourceURL = $sourceURL;
    if($sourceBody)
      $this->_sourceBody = $sourceBody;
    else
      $this->_sourceBody = static::_get($sourceURL);

    // Find all external links in the source
    preg_match_all("/<a[^>]+href=.(https?:\/\/[^'\"]+)/i", $this->_sourceBody, $matches);
    $this->_links = array_unique($matches[1]);
  }
  
  public function setProxy($proxy_string) {
      $this->_proxy = $proxy_string;
      self::$_proxyStatic = $proxy_string;
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
  
  public static function sendPingback($endpoint, $source, $target) {    
    $payload = xmlrpc_encode_request('pingback.ping', array($source,  $target));

    $response = static::_post($endpoint, $payload, array(
      'Content-type: application/xml'
    ));

    if(is_array(xmlrpc_decode($response))):
        return false;
    elseif(is_string($response) && !empty($response)): 
        return true;
    endif;
  }

  public function sendPingbackPayload($target) {
    self::_debug_("Sending pingback now!");

    $pingbackServer = $this->c('pingbackServer', $target);
    $this->_debug("Sending to pingback server: " . $pingbackServer);

    return self::sendPingback($pingbackServer, $this->_sourceURL, $target);
  }

  public function _findWebmentionEndpointInHTML($body, $targetURL=false) {
    $endpoint = false;
    if(preg_match('/<(?:link|a)[ ]+href="([^"]+)"[ ]+rel="webmention"[ ]*\/?>/i', $body, $match)
        || preg_match('/<(?:link|a)[ ]+rel="webmention"[ ]+href="([^"]+)"[ ]*\/?>/i', $body, $match)) {
      $endpoint = $match[1];
    } elseif(preg_match('/<(?:link|a)[ ]+href="([^"]+)"[ ]+rel="http:\/\/webmention\.org\/?"[ ]*\/?>/i', $body, $match)
        || preg_match('/<(?:link|a)[ ]+rel="http:\/\/webmention\.org\/?"[ ]+href="([^"]+)"[ ]*\/?>/i', $body, $match)) {
      $endpoint = $match[1];
    }
    if($endpoint && $targetURL && function_exists('\mf2\resolveUrl')) {
      // Resolve the URL if it's relative
      $endpoint = \mf2\resolveUrl($targetURL, $endpoint);
    }
    return $endpoint;
  }

  public function _findWebmentionEndpointInHeader($link_header, $targetURL=false) {
    $endpoint = false;
    if(preg_match('~<((?:https?://)?[^>]+)>; rel="webmention"~', $link_header, $match)) {
      $endpoint = $match[1];
    } elseif(preg_match('~<((?:https?://)?[^>]+)>; rel="http://webmention.org/?"~', $link_header, $match)) {
      $endpoint = $match[1];
    }
    if($endpoint && $targetURL && function_exists('\mf2\resolveUrl')) {
      // Resolve the URL if it's relative
      $endpoint = \mf2\resolveUrl($targetURL, $endpoint);
    }
    return $endpoint;
  }

  public function supportsWebmention($target) {

    if($this->c('supportsWebmention', $target) === null) {
      $this->c('supportsWebmention', $target, false);

      // First try a HEAD request and look for Link header 
      if(!$this->c('headers', $target)) {
        $this->c('headers', $target, $this->_fetchHead($target));
      }

      $headers = $this->c('headers', $target);
      
      $link_header = false;
      
      if(array_key_exists('Link', $headers)) {
        if(is_array($headers['Link'])) {
          $link_header = implode($headers['Link'], ", ");
        } else {
          $link_header = $headers['Link'];
        }
      }

      if($link_header && ($endpoint=$this->_findWebmentionEndpointInHeader($link_header, $target))) {
        $this->_debug("Found webmention server in header");
        $this->c('webmentionServer', $target, $endpoint);
        $this->c('supportsWebmention', $target, true);
      } else {
        $this->_debug("No webmention server found in header, looking in the body now");
        if(!$this->c('body', $target)) {
          $this->c('body', $target, $this->_fetchBody($target));
        }
        if($endpoint=$this->_findWebmentionEndpointInHTML($this->c('body', $target), $target)) {
          $this->c('webmentionServer', $target, $endpoint);
          $this->c('supportsWebmention', $target, true);
        }
      }

      $this->_debug("webmention server: " . $this->c('webmentionServer', $target));
    }

    return $this->c('supportsWebmention', $target);
  }
  
  public static function sendWebmention($endpoint, $source, $target) {
    
    $payload = http_build_query(array(
      'source' => $source,
      'target' => $target
    ));

    $response = static::_post($endpoint, $payload, array(
      'Content-type: application/x-www-form-urlencoded',
      'Accept: application/json'
    ), true);

    // Return true if the remote endpoint accepted it
    return in_array($response, array(200,202));
  }

  public function sendWebmentionPayload($target) {
    
    $this->_debug("Sending webmention now!");

    $webmentionServer = $this->c('webmentionServer', $target);
    $this->_debug("Sending to webmention server: " . $webmentionServer);

    return self::sendWebmention($webmentionServer, $this->_sourceURL, $target);
  }

  public function sendSupportedMentions($target=false) {

    if($target == false) {
      $totalAccepted = 0;

      foreach($this->_links as $link) {
        $this->_debug("Checking $link");
        $path = parse_url($link, PHP_URL_PATH);
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        if (empty($ext) || !in_array($ext, ['jpg','jpeg','mp4','gif','pdf','png'])) {
          $totalAccepted += $this->sendSupportedMentions($link);
        }
        $this->_debug('');
      }

      return $totalAccepted;
    }

    $accepted = false;

    // Look for a webmention endpoint first
    if($this->supportsWebmention($target)) {
      $accepted = $this->sendWebmentionPayload($target);
    // Only look for a pingback server if we didn't find a webmention server
    } else 
    if($this->supportsPingback($target)) {
      $accepted = $this->sendPingbackPayload($target);
    }

    if($accepted)
      return 1;
    else
      return 0;
  }

  public function debug($enabled) {
    $this->_debugging = $enabled;
    self::enableDebug($enabled);
  }
  public static function enableDebug($enabled) {
    self::$_debugStatic = $enabled;    
  }
  private function _debug($msg) {
    if($this->_debugging)
      echo "\t" . $msg . "\n";
  }
  private static function _debug_($msg) {
    if(self::$_debugStatic)
      echo "\t" . $msg . "\n";
  }

  protected function _fetchHead($url) {
    $this->_debug("Fetching headers...");
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    if ($this->_proxy) curl_setopt($ch, CURLOPT_PROXY, $this->_proxy);
    $response = curl_exec($ch);
    return $this->_parse_headers($response);
  }

  protected function _fetchBody($url) {
    $this->_debug("Fetching body...");
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if ($this->_proxy) curl_setopt($ch, CURLOPT_PROXY, $this->_proxy);
    return curl_exec($ch);
  }

  public function _parse_headers($headers) {
    $retVal = array();
    $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $headers));
    foreach($fields as $field) {
      if(preg_match('/([^:]+): (.+)/m', $field, $match)) {
        //$match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(trim($match[1])));
        $match[1] = preg_replace_callback('/(?<=^|[\x09\x20\x2D])./', function($m) {
          return strtoupper($m[0]);
        }, strtolower(trim($match[1])));
        if(isset($retVal[$match[1]])) {
          $retVal[$match[1]] = array($retVal[$match[1]], $match[2]);
        } else {
          $retVal[$match[1]] = trim($match[2]);
        }
      }
    }
    return $retVal;
  }

  private static function _get($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if (self::$_proxyStatic) curl_setopt($ch, CURLOPT_PROXY, self::$_proxyStatic);
    return curl_exec($ch);
  }

  private static function _post($url, $body, $headers=array(), $returnHTTPCode=false) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if (self::$_proxyStatic) curl_setopt($ch, CURLOPT_PROXY, self::$_proxyStatic);
    $response = curl_exec($ch);
    self::_debug_($response);
    if($returnHTTPCode)
      return curl_getinfo($ch, CURLINFO_HTTP_CODE);
    else
      return $response;
  }

  public function c($type, $url, $val=null) {
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

if (!function_exists('xmlrpc_encode_request')) {
  function xmlrpc_encode_request($method, $params) {
    $xml  = '<?xml version="1.0"?>';
    $xml .= '<methodCall>';
    $xml .= '<methodName>'.htmlspecialchars($method).'</methodName>';
    $xml .= '<params>';
    foreach ($params as $param) {
      $xml .= '<param><value><string>'.htmlspecialchars($param).'</string></value></param>';
    }
    $xml .= '</params></methodCall>';
    
    return $xml;
  }
}
