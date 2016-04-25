<?php
class SendMentionsTest extends PHPUnit_Framework_TestCase {

  public $client;

  public function setUp() {
    $this->client = new IndieWeb\MentionClientTest(false, 'empty');
  }

  public function testFromBodyWithNoLinks() {
    $total = $this->client->sendMentions('http://source.example.com/no-links.html', '<p>No links here</p>');
    $this->assertEquals(0, $total);
  }

  public function testFromURLWithNoLinks() {
    $total = $this->client->sendMentions('http://source.example.com/no-links.html');
    $this->assertEquals(0, $total);
  }

  public function testFromURLWithTwoValidLinks() {
    $total = $this->client->sendMentions('http://source.example.com/two-valid-links.html');
    $this->assertEquals(2, $total);
  }

  public function testFromURLWithOneValidAndOneInvalidLink() {
    $total = $this->client->sendMentions('http://source.example.com/mixed-success-links.html');
    $this->assertEquals(1, $total);
  }

  public function testDoesNotSendToLinksOutsideHEntry() {
    $total = $this->client->sendMentions('http://source.example.com/send-to-h-entry-links.html');
    $this->assertEquals(1, $total);
  }

  public function testPrioritizesWebmentionEndpointOverPingback() {
    $result = $this->client->sendFirstSupportedMention('http://source.example.com/example.html', 'http://target.example.com/header.html');
    $this->assertEquals('webmention', $result);
  }

  public function testFindsPingbackEndpointBecauseNoWebmentionEndpoint() {
    $result = $this->client->sendFirstSupportedMention('http://source.example.com/example.html', 'http://target.example.com/only-pingback.html');
    $this->assertEquals('pingback', $result);
  }

  public function testDoesNotSendPingbackDespiteWebmentionFail() {
    $result = $this->client->sendFirstSupportedMention('http://source.example.com/example.html', 'http://target.example.com/webmention-failed.html');
    $this->assertEquals(false, $result);
  }

  public function testSendsFailingPingback() {
    $result = $this->client->sendFirstSupportedMention('http://source.example.com/example.html', 'http://target.example.com/pingback-failed.html');
    $this->assertEquals(false, $result);
  }

  public function testSendsFailingWebmention() {
    $result = $this->client->sendFirstSupportedMention('http://source.example.com/example.html', 'http://target.example.com/webmention-only-failed.html');
    $this->assertEquals(false, $result);
  }

  public function testSendsWebmentionAndWasCreated() {
    $result = $this->client->sendFirstSupportedMention('http://source.example.com/example.html', 'http://target.example.com/webmention-created.html');
    $this->assertEquals('webmention', $result);
  }

}
