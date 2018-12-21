<?php
class TagParserTest extends PHPUnit_Framework_TestCase {

  public $client;

  public function setUp() {
    $this->client = new IndieWeb\MentionClientTest(false, 'empty');
  }

  public function testFindWebmentionTagRelWebmentionHref() {
    $html = '<link rel="webmention" href="http://example.com/webmention" />';
    $endpoint = $this->client->_findWebmentionEndpointInHTML($html);
    $this->assertEquals('http://example.com/webmention', $endpoint);
  }

  public function testFindWebmentionATagRelWebmentionHref() {
    $html = '<a rel="webmention" href="http://example.com/webmention">this site supports webmention</a>';
    $endpoint = $this->client->_findWebmentionEndpointInHTML($html);
    $this->assertEquals('http://example.com/webmention', $endpoint);
  }

  public function testFindWebmentionATagRelSpaceBeforeWebmentionHref() {
    $html = '<a rel="pings webmention" href="http://example.com/webmention">this site supports webmention</a>';
    $endpoint = $this->client->_findWebmentionEndpointInHTML($html);
    $this->assertEquals('http://example.com/webmention', $endpoint);
  }

  public function testFindWebmentionATagRelSpaceAfterWebmentionHref() {
    $html = '<a rel="webmention pings" href="http://example.com/webmention">this site supports webmention</a>';
    $endpoint = $this->client->_findWebmentionEndpointInHTML($html);
    $this->assertEquals('http://example.com/webmention', $endpoint);
  }

  public function testFindWebmentionLinkTagRelSpaceAroundWebmentionHref() {
    $html = '<link rel="beeboop webmention pings" href="http://example.com/webmention" />';
    $endpoint = $this->client->_findWebmentionEndpointInHTML($html);
    $this->assertEquals('http://example.com/webmention', $endpoint);
  }

  public function testFindWebmentionTagHrefRelWebmention() {
    $html = '<link href="http://example.com/webmention" rel="webmention" />';
    $endpoint = $this->client->_findWebmentionEndpointInHTML($html);
    $this->assertEquals('http://example.com/webmention', $endpoint);
  }

  public function testFindWebmentionTagRelNoSlashHref() {
    $html = '<link rel="http://webmention.org" href="http://example.com/webmention" />';
    $endpoint = $this->client->_findWebmentionEndpointInHTML($html);
    $this->assertEquals('http://example.com/webmention', $endpoint);
  }

  public function testFindWebmentionTagHrefRelNoSlash() {
    $html = '<link href="http://example.com/webmention" rel="http://webmention.org" />';
    $endpoint = $this->client->_findWebmentionEndpointInHTML($html);
    $this->assertEquals('http://example.com/webmention', $endpoint);
  }

  public function testFindWebmentionTagRelHref() {
    $html = '<link rel="http://webmention.org/" href="http://example.com/webmention" />';
    $endpoint = $this->client->_findWebmentionEndpointInHTML($html);
    $this->assertEquals('http://example.com/webmention', $endpoint);
  }

  public function testFindWebmentionTagHrefRel() {
    $html = '<link href="http://example.com/webmention" rel="http://webmention.org/" />';
    $endpoint = $this->client->_findWebmentionEndpointInHTML($html);
    $this->assertEquals('http://example.com/webmention', $endpoint);
  }

  public function testFindWebmentionTagExtraWhitespace() {
    $html = '<link  href="http://example.com/webmention"   rel="http://webmention.org/"  />';
    $endpoint = $this->client->_findWebmentionEndpointInHTML($html);
    $this->assertEquals('http://example.com/webmention', $endpoint);
  }

  public function testFindWebmentionTagNoWhitespace() {
    $html = '<link href="http://example.com/webmention" rel="http://webmention.org/"/>';
    $endpoint = $this->client->_findWebmentionEndpointInHTML($html);
    $this->assertEquals('http://example.com/webmention', $endpoint);
  }

  public function testFindWebmentionTagNoCloseTag() {
    $html = '<link href="http://example.com/webmention" rel="http://webmention.org/">';
    $endpoint = $this->client->_findWebmentionEndpointInHTML($html);
    $this->assertEquals('http://example.com/webmention', $endpoint);
  }

  public function testFindWebmentionTagRelativeUrl() {
    $html = '<link href="/webmention" rel="webmention">';
    $endpoint = $this->client->_findWebmentionEndpointInHTML($html, 'http://example.com/post/1');
    $this->assertEquals('http://example.com/webmention', $endpoint);
  }

  public function testFindWebmentionTagMultipleRels() {
    $html = '<link href="/webmention" rel="webmention http://webmention.org">';
    $endpoint = $this->client->_findWebmentionEndpointInHTML($html, 'http://example.com/post/1');
    $this->assertEquals('http://example.com/webmention', $endpoint);
  }

  public function testParseProtocolRelativeURL() {
    $html = '<link href="//example.com/webmention" rel="webmention">';
    $endpoint = $this->client->_findWebmentionEndpointInHTML($html, 'https://example.com/post/1');
    $this->assertEquals('https://example.com/webmention', $endpoint);
  }

}
