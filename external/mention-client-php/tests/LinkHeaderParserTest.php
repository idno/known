<?php
class LinkHeaderParserTest extends PHPUnit_Framework_TestCase {

  public $client;

  public function setUp() {
    $this->client = new IndieWeb\MentionClientTest(false, 'empty');
  }

  public function testParsesMultipleHeadersToArray() {
    $headers = "HTTP/1.1 200 OK\r
Link: <http://aaronparecki.com/webmention.php>; rel=\"http://webmention.org/\"\r
Link: <http://aaronparecki.com/webmention.php>; rel=\"webmention\"\r
Link: <http://aaronparecki.com/>; rel=\"me\"\r
";

    $headers = IndieWeb\MentionClientTest::_parse_headers($headers);
    $this->assertInternalType('array', $headers['Link']);
  }

  public function testNormalizesHeaderNameCase() {
    $headers = "HTTP/1.1 200 OK\r
One-two: three\r
four-five: six\r
Seven-Eight: nine\r
TEN-ELEVEN: twelve\r
";

    $headers = IndieWeb\MentionClientTest::_parse_headers($headers);
    $this->assertArrayHasKey('One-Two', $headers);
    $this->assertArrayHasKey('Four-Five', $headers);
    $this->assertArrayHasKey('Seven-Eight', $headers);
    $this->assertArrayHasKey('Ten-Eleven', $headers);
  }

  public function testFindWebmentionLinkHeader() {
  	$headers = "HTTP/1.1 200 OK\r
Server: nginx/1.0.14\r
Date: Thu, 04 Jul 2013 15:56:21 GMT\r
Content-Type: text/html; charset=UTF-8\r
Connection: keep-alive\r
X-Powered-By: PHP/5.3.13\r
X-Pingback: http://pingback.me/webmention?forward=http%3A%2F%2Faaronparecki.com%2Fwebmention.php\r
Link: <http://aaronparecki.com/webmention.php>; rel=\"http://webmention.org/\"\r
Link: <http://aaronparecki.com/webmention.php>; rel=\"webmention\"\r
Link: <http://aaronparecki.com/>; rel=\"me\"\r
";

    $target = 'http://example.com/';
    $this->client->c('headers', $target, IndieWeb\MentionClientTest::_parse_headers($headers));
    $endpoint = $this->client->discoverWebmentionEndpoint($target);
    $this->assertEquals('http://aaronparecki.com/webmention.php', $endpoint);
  }

  public function testFindWebmentionHeaderRelWebmention() {
    $header = '<http://example.com/webmention>; rel="webmention"';
    $endpoint = $this->client->_findWebmentionEndpointInHeader($header);
    $this->assertEquals('http://example.com/webmention', $endpoint);
  }

  public function testFindWebmentionHeaderRelWebmentionOrg() {
    $header = '<http://example.com/webmention>; rel="http://webmention.org"';
    $endpoint = $this->client->_findWebmentionEndpointInHeader($header);
    $this->assertEquals('http://example.com/webmention', $endpoint);
  }

  public function testFindWebmentionHeaderRelWebmentionOrgSlash() {
    $header = '<http://example.com/webmention>; rel="http://webmention.org/"';
    $endpoint = $this->client->_findWebmentionEndpointInHeader($header);
    $this->assertEquals('http://example.com/webmention', $endpoint);
  }

  public function testFindWebmentionLinkHeaderWithMultipleLinks() {
    $headers = "HTTP/1.1 200 OK\r
Link: <http://pubsubhubbub.appspot.com>; rel=\"hub\", <http://pubsubhubbub.superfeedr.com>; rel=\"hub\", <http://notizblog.org/>; rel=\"self\", <http://pubsubhubbub.superfeedr.com>; rel=\"hub\", <http://notizblog.org/>; rel=\"self\", <http://notizblog.org/?webmention=endpoint>; rel=\"http://webmention.org/\", <http://notizblog.org/>; rel=shortlink\r
    ";

    $target = 'http://aaronparecki.com/';
    $this->client->c('headers', $target, IndieWeb\MentionClientTest::_parse_headers($headers));
    $endpoint = $this->client->discoverWebmentionEndpoint($target);
    $this->assertEquals('http://notizblog.org/?webmention=endpoint', $endpoint);
  }

  public function testFindWebmentionHeaderRelativeUrl() {
    $header = '</webmention>; rel="webmention"';
    $endpoint = $this->client->_findWebmentionEndpointInHeader($header, 'http://example.com/post/1');
    $this->assertEquals('http://example.com/webmention', $endpoint);
  }

}
