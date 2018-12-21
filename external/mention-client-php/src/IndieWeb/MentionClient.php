<?php
namespace IndieWeb;

class MentionClient {

  private static $_debugEnabled = false;

  private $_sourceBody;

  private $_links = array();

  private $_headers = array();
  private $_body = array();
  private $_rels = array();
  private $_supportsPingback = array();
  private $_supportsWebmention = array();
  private $_pingbackServer = array();
  private $_webmentionServer = array();

  private static $_proxy = false;

  public $usemf2 = true; // for testing, can set this to false to avoid using the Mf2 parser

  /**
   * @codeCoverageIgnore
   */
  public function setProxy($proxy_string) {
    self::$_proxy = $proxy_string;
  }

  public function discoverPingbackEndpoint($target) {

    if($this->c('supportsPingback', $target) === null) {
      $this->c('supportsPingback', $target, false);

      // First try a HEAD request and look for X-Pingback header
      if(!$this->c('headers', $target)) {
        $head = static::_head($target);
        $this->c('headers', $target, $head['headers']);
      }

      $headers = $this->c('headers', $target);
      if(array_key_exists('X-Pingback', $headers)) {
        self::_debug("Found pingback server in header");
        $this->c('pingbackServer', $target, $headers['X-Pingback']);
        $this->c('supportsPingback', $target, true);
      } else {
        self::_debug("No pingback server found in header, looking in the body now");
        if(!$this->c('body', $target)) {
          $body = static::_get($target);
          $this->c('body', $target, $body['body']);
          $this->_parseBody($target, $body['body']);
        }
        if($rels=$this->c('rels', $target)) {
          // If the mf2 parser is present, then rels will have been set, and use that instead
          if(count($rels)) {
            if(array_key_exists('pingback', $rels)) {
              $this->c('pingbackServer', $target, $rels['pingback'][0]);
              $this->c('supportsPingback', $target, true);
            }
          }
        } else {
          $body = $this->c('body', $target);
          if(preg_match("/<link rel=\"pingback\" href=\"([^\"]+)\" ?\/?>/i", $body, $match)) {
            $this->c('pingbackServer', $target, $match[1]);
            $this->c('supportsPingback', $target, true);
          }
        }
      }

      self::_debug("pingback server: " . $this->c('pingbackServer', $target));
    }

    return $this->c('pingbackServer', $target);
  }

  public static function sendPingbackToEndpoint($endpoint, $source, $target) {
    self::_debug("Sending pingback now!");

    $payload = static::xmlrpc_encode_request('pingback.ping', array($source,  $target));

    $response = static::_post($endpoint, $payload, array(
      'Content-type: application/xml'
    ));

    if($response['code'] != 200 || empty($response['body']))
      return false;

     // collapse whitespace just to be safe
     $body = strtolower(preg_replace('/\s+/', '', $response['body']));

     // successful response MUST contain a single string
     return $body && strpos($body, '<fault>') === false && strpos($body, '<string>') !== false;
  }

  public function sendPingback($sourceURL, $targetURL) {

    // If we haven't discovered the pingback endpoint yet, do it now
    if($this->c('supportsPingback', $targetURL) === null) {
      $this->discoverPingbackEndpoint($targetURL);
    }

    $pingbackServer = $this->c('pingbackServer', $targetURL);
    if($pingbackServer) {
      self::_debug("Sending to pingback server: " . $pingbackServer);
      return self::sendPingbackToEndpoint($pingbackServer, $sourceURL, $targetURL);
    } else {
      return false;
    }
  }

  protected function _parseBody($target, $html) {
    if(class_exists('\Mf2\Parser') && $this->usemf2) {
      $parser = new \Mf2\Parser($html, $target);
      list($rels, $alternates) = $parser->parseRelsAndAlternates();
      $this->c('rels', $target, $rels);
    }
  }

  protected function _findWebmentionEndpointInHTML($body, $targetURL=false) {
    $endpoint = false;

    $body = preg_replace('/<!--(.*)-->/Us', '', $body);
    if(preg_match('/<(?:link|a)[ ]+href="([^"]*)"[ ]+rel="[^" ]* ?webmention ?[^" ]*"[ ]*\/?>/i', $body, $match)
        || preg_match('/<(?:link|a)[ ]+rel="[^" ]* ?webmention ?[^" ]*"[ ]+href="([^"]*)"[ ]*\/?>/i', $body, $match)) {
      $endpoint = $match[1];
    }
    if($endpoint !== false && $targetURL && function_exists('\Mf2\resolveUrl')) {
      // Resolve the URL if it's relative
      $endpoint = \Mf2\resolveUrl($targetURL, $endpoint);
    }
    return $endpoint;
  }

  protected function _findWebmentionEndpointInHeader($link_header, $targetURL=false) {
    $endpoint = false;
    if(preg_match('~<((?:https?://)?[^>]+)>; rel="?(?:https?://webmention.org/?|webmention)"?~', $link_header, $match)) {
      $endpoint = $match[1];
    }
    if($endpoint && $targetURL && function_exists('\Mf2\resolveUrl')) {
      // Resolve the URL if it's relative
      $endpoint = \Mf2\resolveUrl($targetURL, $endpoint);
    }
    return $endpoint;
  }

  public function discoverWebmentionEndpoint($target) {

    if($this->c('supportsWebmention', $target) === null) {
      $this->c('supportsWebmention', $target, false);

      // First try a HEAD request and look for Link header
      if(!$this->c('headers', $target)) {
        $head = static::_head($target);
        $this->c('headers', $target, $head['headers']);
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
        self::_debug("Found webmention server in header");
        $this->c('webmentionServer', $target, $endpoint);
        $this->c('supportsWebmention', $target, true);
      } else {
        self::_debug("No webmention server found in header, looking in the body now");
        if(!$this->c('body', $target)) {
          $body = static::_get($target);
          $this->c('body', $target, $body['body']);
          $this->_parseBody($target, $body['body']);
        }
        if($rels=$this->c('rels', $target)) {
          // If the mf2 parser is present, then rels will have been set, so use that instead
          if(count($rels)) {
            if(array_key_exists('webmention', $rels)) {
              $endpoint = $rels['webmention'][0];
              $this->c('webmentionServer', $target, $endpoint);
              $this->c('supportsWebmention', $target, true);
            } elseif(array_key_exists('http://webmention.org/', $rels) || array_key_exists('http://webmention.org', $rels)) {
              $endpoint = $rels[array_key_exists('http://webmention.org/', $rels) ? 'http://webmention.org/' : 'http://webmention.org'][0];
              $this->c('webmentionServer', $target, $endpoint);
              $this->c('supportsWebmention', $target, true);
            }
          }
        } else {
          if($endpoint=$this->_findWebmentionEndpointInHTML($this->c('body', $target), $target)) {
            $this->c('webmentionServer', $target, $endpoint);
            $this->c('supportsWebmention', $target, true);
          }
        }
      }

      self::_debug("webmention server: " . $this->c('webmentionServer', $target));
    }

    return $this->c('webmentionServer', $target);
  }

  public static function sendWebmentionToEndpoint($endpoint, $source, $target, $additional=array()) {

    self::_debug("Sending webmention now!");

    $payload = http_build_query(array_merge(array(
      'source' => $source,
      'target' => $target
    ), $additional));

    return static::_post($endpoint, $payload, array(
      'Content-type: application/x-www-form-urlencoded',
      'Accept: application/json'
    ));
  }

  public function sendWebmention($sourceURL, $targetURL, $additional=array()) {

    // If we haven't discovered the webmention endpoint yet, do it now
    if($this->c('supportsWebmention', $targetURL) === null) {
      $this->discoverWebmentionEndpoint($targetURL);
    }

    $webmentionServer = $this->c('webmentionServer', $targetURL);
    if($webmentionServer) {
      self::_debug("Sending to webmention server: " . $webmentionServer);
      return self::sendWebmentionToEndpoint($webmentionServer, $sourceURL, $targetURL, $additional);
    } else {
      return false;
    }
  }

  public static function findOutgoingLinks($input) {
    // Find all outgoing links in the source
    if(is_string($input)) {
      preg_match_all("/<a[^>]+href=.(https?:\/\/[^'\"]+)/i", $input, $matches);
      return array_unique($matches[1]);
    } elseif(is_array($input) && array_key_exists('items', $input) && array_key_exists(0, $input['items'])) {
      $links = array();

      // Find links in the content HTML
      $item = $input['items'][0];

      if(array_key_exists('content', $item['properties'])) {
        if(is_array($item['properties']['content'][0])) {
          $html = $item['properties']['content'][0]['html'];
          $links = array_merge($links, self::findOutgoingLinks($html));
        } else {
          $text = $item['properties']['content'][0];
          $links = array_merge($links, self::findLinksInText($text));
        }
      }

      // Look at all properties of the item and collect all the ones that look like URLs
      $links = array_merge($links, self::findLinksInJSON($item));

      return array_unique($links);
    } else {
      return array();
    }
  }

  public static function findLinksInText($input) {
    preg_match_all('/https?:\/\/[^ ]+/', $input, $matches);
    return array_unique($matches[0]);
  }

  public static function findLinksInJSON($input) {
    $links = array();
    // This recursively iterates over the whole input array and searches for
    // everything that looks like a URL regardless of its depth or property name
    foreach(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($input)) as $key => $value) {
      if(substr($value, 0, 7) == 'http://' || substr($value, 0, 8) == 'https://')
        $links[] = $value;
    }
    return $links;
  }

  public function sendMentions($sourceURL, $sourceBody=false) {
    if($sourceBody) {
      $this->_sourceBody = $sourceBody;
      $this->_links = self::findOutgoingLinks($sourceBody);
    } else {
      $body = static::_get($sourceURL);
      $this->_sourceBody = $body['body'];
      $parsed = \Mf2\parse($this->_sourceBody, $sourceURL);
      $this->_links = self::findOutgoingLinks($parsed);
    }

    $totalAccepted = 0;

    foreach($this->_links as $target) {
      self::_debug("Checking $target for webmention and pingback endpoints");

      if($this->sendFirstSupportedMention($sourceURL, $target)) {
        $totalAccepted++;
      }
    }

    return $totalAccepted;
  }

  public function sendFirstSupportedMention($source, $target) {

    $accepted = false;

    // Look for a webmention endpoint first
    if($this->discoverWebmentionEndpoint($target)) {
      $result = $this->sendWebmention($source, $target);
      if($result &&
        ($result['code'] == 200
          || $result['code'] == 201
          || $result['code'] == 202)) {
        $accepted = 'webmention';
      }
    // Only look for a pingback server if we didn't find a webmention server
    } else if($this->discoverPingbackEndpoint($target)) {
      $result = $this->sendPingback($source, $target);
      if($result) {
        $accepted = 'pingback';
      }
    }

    return $accepted;
  }

  /**
   * @codeCoverageIgnore
   */
  public static function enableDebug() {
    self::$_debugEnabled = true;
  }
  /**
   * @codeCoverageIgnore
   */
  private static function _debug($msg) {
    if(self::$_debugEnabled)
      echo "\t" . $msg . "\n";
  }

  /**
   * @codeCoverageIgnore
   */
  protected static function _head($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    if (self::$_proxy) curl_setopt($ch, CURLOPT_PROXY, self::$_proxy);
    $response = curl_exec($ch);
    return array(
      'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
      'headers' => self::_parse_headers(trim($response)),
    );
  }

  /**
   * @codeCoverageIgnore
   */
  protected static function _get($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    if (self::$_proxy) curl_setopt($ch, CURLOPT_PROXY, self::$_proxy);
    $response = curl_exec($ch);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    return array(
      'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
      'headers' => self::_parse_headers(trim(substr($response, 0, $header_size))),
      'body' => substr($response, $header_size)
    );
  }

  /**
   * @codeCoverageIgnore
   */
  protected static function _post($url, $body, $headers=array()) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_HEADER, true);
    if (self::$_proxy) curl_setopt($ch, CURLOPT_PROXY, self::$_proxy);
    $response = curl_exec($ch);
    self::_debug($response);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    return array(
      'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
      'headers' => self::_parse_headers(trim(substr($response, 0, $header_size))),
      'body' => substr($response, $header_size)
    );
  }

  protected static function _parse_headers($headers) {
    $retVal = array();
    $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $headers));
    foreach($fields as $field) {
      if(preg_match('/([^:]+): (.+)/m', $field, $match)) {
        $match[1] = preg_replace_callback('/(?<=^|[\x09\x20\x2D])./', function($m) {
          return strtoupper($m[0]);
        }, strtolower(trim($match[1])));
        // If there's already a value set for the header name being returned, turn it into an array and add the new value
        $match[1] = preg_replace_callback('/(?<=^|[\x09\x20\x2D])./', function($m) {
          return strtoupper($m[0]);
        }, strtolower(trim($match[1])));
        if(isset($retVal[$match[1]])) {
          if(!is_array($retVal[$match[1]]))
            $retVal[$match[1]] = array($retVal[$match[1]]);
          $retVal[$match[1]][] = $match[2];
        } else {
          $retVal[$match[1]] = trim($match[2]);
        }
      }
    }
    return $retVal;
  }

  public static function xmlrpc_encode_request($method, $params) {
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
