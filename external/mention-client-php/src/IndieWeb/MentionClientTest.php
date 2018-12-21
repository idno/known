<?php
namespace IndieWeb;

/*
 * Make all protected methods public for PHPUnit
 */
class MentionClientTest extends MentionClient {

  public static $dataDir = null;

  public function __call($method, $args) {
    $method = new \ReflectionMethod('IndieWeb\MentionClient', $method);
    $method->setAccessible(true);
    return $method->invokeArgs($this, $args);
  }

  public static function __callStatic($method, $args) {
    $method = new \ReflectionMethod('IndieWeb\MentionClient', $method);
    $method->setAccessible(true);
    return $method->invokeArgs(null, $args);
  }

  protected static function _head($url) {
    $response = self::_read_file($url);
    return array(
      'code' => $response['code'],
      'headers' => $response['headers']
    );
  }

  protected static function _get($url) {
    return self::_read_file($url);
  }

  protected static function _post($url, $body, $headers=array()) {
    return self::_read_file($url);
  }

  private static function _read_file($url) {
    if(self::$dataDir) {
      $dataDir = self::$dataDir;
    } else {
      $dataDir = dirname(__FILE__).'/../../tests/data/';
    }

    $filename = $dataDir.preg_replace('/https?:\/\//', '', $url);
    if(!file_exists($filename)) {
      $filename = dirname(__FILE__).'/../../tests/data/404.response.txt';
    }
    $response = file_get_contents($filename);

    $split = explode("\r\n\r\n", $response);
    if(count($split) != 2) {
      throw new \Exception("Invalid file contents, check that newlines are CRLF: $url");
    }
    list($headers, $body) = $split;

    if(preg_match('/HTTP\/1\.1 (\d+)/', $headers, $match)) {
      $code = $match[1];
    }

    $headers = preg_replace('/HTTP\/1\.1 \d+ .+/', '', $headers);

    return array(
      'code' => $code,
      'headers' => self::_parse_headers($headers),
      'body' => $body
    );
  }
}
