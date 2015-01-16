<?php
class LinkHeaderParserTest extends PHPUnit_Framework_TestCase {

  public $client;

  public function setUp() {
    $this->client = new IndieWeb\MentionClient(false, 'empty');
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
Link: <http://aaronparecki.com/>; rel=\"me\"";
	
    $target = 'http://aaronparecki.com/';
    $this->client->c('headers', $target, $this->client->_parse_headers($headers));
    $supports = $this->client->supportsWebmention($target);
    $this->assertEquals(true, $supports);
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
    $this->client->c('headers', $target, $this->client->_parse_headers($headers));
    $supports = $this->client->supportsWebmention($target);
    $this->assertEquals(true, $supports);
  }

  public function testFindWebmentionHeaderRelativeUrl() {
    $header = '</webmention>; rel="webmention"';
    $endpoint = $this->client->_findWebmentionEndpointInHeader($header, 'http://example.com/post/1');
    $this->assertEquals('http://example.com/webmention', $endpoint);
  }

}
