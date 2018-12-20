<?php
namespace IndieWeb;
/**
 * Class MentionClient supports webmention, pingback and endpoint discovery.
 * @package IndieWeb
 */
class MentionClient {

  private static $_debugEnabled = false;

  private $_sourceBody;

  /**
   * @var array set of links to be checked for mentions.
   */
  private $_links = array();

  private $_headers = array();
  private $_body = array();
  private $_rels = array();
  private $_supportsPingback = array();
  private $_supportsWebmention = array();
  private $_pingbackServer = array();
  private $_webmentionServer = array();

  private static $_proxy = false;
  private static $_userAgent = false;

  public $usemf2 = true; // for testing, can set this to false to avoid using the Mf2 parser

  /**
   * @param string $proxy_string
   * @codeCoverageIgnore
   */
  public function setProxy($proxy_string) {
    self::$_proxy = $proxy_string;
  }

  /**
   * @param string $user_agent
   * @codeCoverageIgnore
   */
  public static function setUserAgent($user_agent) {
    self::$_userAgent = $user_agent;
  }

  /**
   * Looks for pingback URL target. sets attributes on $this->c .
   * @param string $target URL
   * @return mixed setting $this->c('pingbackServer', $target);
   */
  public function discoverPingbackEndpoint($target) {

    if($this->c('supportsPingback', $target) === null) {
      $this->c('supportsPingback', $target, false);

      // First try a HEAD request and look for X-Pingback header
      if(!$this->c('headers', $target)) {
        $head = static::_head($target);
        $target = $head['url'];
        $this->c('headers', $target, $head['headers']);
      }

      $headers = $this->c('headers', $target);
      if(array_key_exists('X-Pingback', $headers)) {
        self::_debug("discoverPingbackEndpoint: Found pingback server in header");
        $this->c('pingbackServer', $target, $headers['X-Pingback']);
        $this->c('supportsPingback', $target, true);
      } else {
        self::_debug("discoverPingbackEndpoint: No pingback server found in header, looking in the body now");
        if(!$this->c('body', $target)) {
          $body = static::_get($target);
          $target = $body['url'];
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

      self::_debug("discoverPingbackEndpoint: pingback server: " . $this->c('pingbackServer', $target));
    }

    return $this->c('pingbackServer', $target);
  }

  /**
   * Sends pingback to endpoints
   * @param $endpoint string URL for pingback listener
   * @param $source string originating post URL
   * @param $target string URL like permalink of target post
   * @return bool Successful response MUST contain a single string
   */
  public static function sendPingbackToEndpoint($endpoint, $source, $target) {
    self::_debug("sendPingbackToEndpoint: Sending pingback now!");

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

  /**
   * Public function to send pingbacks to $targetURL
   * @param $sourceURL string URL for source of pingback
   * @param $targetURL string URL for destination of pingback
   * @return bool runs sendPingbackToEndpoint().
   * @see MentionClient::sendPingbackToEndpoint()
   */
  public function sendPingback($sourceURL, $targetURL) {

    // If we haven't discovered the pingback endpoint yet, do it now
    if($this->c('supportsPingback', $targetURL) === null) {
      $this->discoverPingbackEndpoint($targetURL);
    }

    $pingbackServer = $this->c('pingbackServer', $targetURL);
    if($pingbackServer) {
      self::_debug("sendPingback: Sending to pingback server: " . $pingbackServer);
      return self::sendPingbackToEndpoint($pingbackServer, $sourceURL, $targetURL);
    } else {
      return false;
    }
  }

  /**
   * Parses body of html. Protected method.
   * @param $target string the URL of the target page
   * @param $html string the HTML of page
   */
  protected function _parseBody($target, $html) {
    if(class_exists('\Mf2\Parser') && $this->usemf2) {
      $parser = new \Mf2\Parser($html, $target);
      list($rels, $alternates) = $parser->parseRelsAndAlternates();
      $this->c('rels', $target, $rels);
    }
  }

  /**
   * finds webmention endpoints in the body. protected function
   * @param $body
   * @param string $targetURL
   * @return bool
   */
  protected function _findWebmentionEndpointInHTML($body, $targetURL = false) {
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

  /**
   * @param $link_header
   * @param string $targetURL
   * @return bool
   */
  protected function _findWebmentionEndpointInHeader($link_header, $targetURL = false) {
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

  /**
   * Finds webmention endpoints at URL. Examines header request.
   * Also modifies $this->c to indicate if $target accepts webmention
   * @param $target string the URL to examine for endpoints.
   * @return mixed
   */
  public function discoverWebmentionEndpoint($target) {

    if($this->c('supportsWebmention', $target) === null) {
      $this->c('supportsWebmention', $target, false);

      // First try a HEAD request and look for Link header
      if(!$this->c('headers', $target)) {
        $head = static::_head($target);
        $target = $head['url'];
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
        self::_debug("discoverWebmentionEndpoint: Found webmention server in header");
        $this->c('webmentionServer', $target, $endpoint);
        $this->c('supportsWebmention', $target, true);
      } else {
        self::_debug("discoverWebmentionEndpoint: No webmention server found in header, looking in body now");
        if(!$this->c('body', $target)) {
          $body = static::_get($target);
          $target = $body['url'];
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

      self::_debug("discoverWebmentionEndpoint: webmention server: " . $this->c('webmentionServer', $target));
    }

    return $this->c('webmentionServer', $target);
  }

  /**
   * Static function can send a webmention to an endpoint via static::_post
   * @param $endpoint string URL of endpoint detected
   * @param $source string URL of originating post (other server will check probably)
   * @param $target string URL of target post
   * @param array $additional extra optional stuff that will be included in payload.
   * @return array
   */
  public static function sendWebmentionToEndpoint($endpoint, $source, $target, $additional = array()) {

    self::_debug("sendWebmentionToEndpoint: Sending webmention now!");

    $payload = http_build_query(array_merge(array(
      'source' => $source,
      'target' => $target
    ), $additional));

    return static::_post($endpoint, $payload, array(
      'Content-type: application/x-www-form-urlencoded',
      'Accept: application/json, */*;q=0.8'
    ));
  }

  /**
   * Sends webmention to a target url. may use
   * @param $sourceURL
   * @param $targetURL
   * @param array $additional
   * @return array|bool
   * @see MentionClient::sendWebmentionToEndpoint()
   */
  public function sendWebmention($sourceURL, $targetURL, $additional = array()) {

    // If we haven't discovered the webmention endpoint yet, do it now
    if($this->c('supportsWebmention', $targetURL) === null) {
      $this->discoverWebmentionEndpoint($targetURL);
    }

    $webmentionServer = $this->c('webmentionServer', $targetURL);
    if($webmentionServer) {
      self::_debug("sendWebmention: Sending to webmention server: " . $webmentionServer);
      return self::sendWebmentionToEndpoint($webmentionServer, $sourceURL, $targetURL, $additional);
    } else {
      return false;
    }
  }

  /**
   * Scans outgoing links in block of text $input.
   * @param $input string html block.
   * @return array array of unique links or empty.
   */
  public static function findOutgoingLinks($input) {
    // Find all outgoing links in the source
    if(is_string($input)) {
      preg_match_all("/<a[^>]+href=.(https?:\/\/[^'\"]+)/i", $input, $matches);
      return array_unique($matches[1]);
    } elseif (is_array($input) && array_key_exists('items', $input) && array_key_exists(0, $input['items'])) {
      $links = array();

      // Find links in the content HTML
      $item = $input['items'][0];

      if (array_key_exists('content', $item['properties'])) {
        if (is_array($item['properties']['content'][0])) {
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

  /**
   * find all links in text.
   * @param $input string text block
   * @return mixed array of links in text block.
   */
  public static function findLinksInText($input) {
    preg_match_all('/https?:\/\/[^ ]+/', $input, $matches);
    return array_unique($matches[0]);
  }

  /**
   * find links in JSON input string.
   * @param $input string JSON object.
   * @return array of links in JSON object.
   */
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

  /**
   * Tries to send webmention and pingbacks to each link on $sourceURL. Depends on Microformats2
   * @param $sourceURL string URL to examine to send mentions to
   * @param bool $sourceBody if true will search for outgoing links with this (string).
   * @return int
   * @see \Mf2\parse
   */
  public function sendMentions($sourceURL, $sourceBody = false) {
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
      self::_debug("sendMentions: Checking $target for webmention and pingback endpoints");

      if($this->sendFirstSupportedMention($sourceURL, $target)) {
        $totalAccepted++;
      }
    }

    return $totalAccepted;
  }

  /**
   * @param $source
   * @param $target
   * @return bool|string
   */
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
   * Enables debug messages to appear during activity. Not recommended for production use.
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
   * @param $url
   * @return array
   * @codeCoverageIgnore
   */
  protected static function _head($url, $headers=array()) {
    if(self::$_userAgent)
      $headers[] = 'User-Agent: '.self::$_userAgent;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    if (self::$_proxy) curl_setopt($ch, CURLOPT_PROXY, self::$_proxy);
    $response = curl_exec($ch);
    return array(
      'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
      'headers' => self::_parse_headers(trim($response)),
      'url' => curl_getinfo($ch, CURLINFO_EFFECTIVE_URL)
    );
  }

  /**
   * Protected static function
   * @param $url string URL to grab through curl.
   * @return array with keys 'code' 'headers' and 'body'
   * @codeCoverageIgnore
   */
  protected static function _get($url, $headers=array()) {
    if(self::$_userAgent)
      $headers[] = 'User-Agent: '.self::$_userAgent;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    if (self::$_proxy) curl_setopt($ch, CURLOPT_PROXY, self::$_proxy);
    $response = curl_exec($ch);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    return array(
      'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
      'headers' => self::_parse_headers(trim(substr($response, 0, $header_size))),
      'body' => substr($response, $header_size),
      'url' => curl_getinfo($ch, CURLINFO_EFFECTIVE_URL)
    );
  }

  /**
   * @param $url
   * @param $body
   * @param array $headers
   * @return array
   * @codeCoverageIgnore
   */
  protected static function _post($url, $body, $headers=array()) {
    if(self::$_userAgent)
      $headers[] = 'User-Agent: '.self::$_userAgent;

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

  /**
   * Protected static function to parse headers.
   * @param $headers
   * @return array
   */
  protected static function _parse_headers($headers) {
    $retVal = array();
    $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $headers));
    foreach($fields as $field) {
      if (preg_match('/([^:]+): (.+)/m', $field, $match)) {
        $match[1] = preg_replace_callback('/(?<=^|[\x09\x20\x2D])./', function($m) {
          return strtoupper($m[0]);
        }, strtolower(trim($match[1])));
        // If there's already a value set for the header name being returned, turn it into an array and add the new value
        $match[1] = preg_replace_callback('/(?<=^|[\x09\x20\x2D])./', function($m) {
          return strtoupper($m[0]);
        }, strtolower(trim($match[1])));
        if (isset($retVal[$match[1]])) {
          if (!is_array($retVal[$match[1]]))
            $retVal[$match[1]] = array($retVal[$match[1]]);
          $retVal[$match[1]][] = $match[2];
        } else {
          $retVal[$match[1]] = trim($match[2]);
        }
      }
    }
    return $retVal;
  }

  /**
   * Static function for XML-RPC encoding request.
   * @param $method string goes into MethodName XML tag
   * @param $params array set of strings that go into param/value XML tags.
   * @return string
   */
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

  /**
   * Caching key/value system for MentionClient
   * @param $type
   * @param $url
   * @param mixed $val If not null, is set to default value
   * @return mixed
   */
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
