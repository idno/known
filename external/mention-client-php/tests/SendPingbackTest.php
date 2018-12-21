<?php
class SendPingbackTest extends PHPUnit_Framework_TestCase {

  public $client;

  public function setUp() {
    $this->client = new IndieWeb\MentionClientTest(false, 'empty');
  }

  public function testNot200Response() {
    $endpoint = 'http://pingback-endpoint.example/404-response';
    $response = $this->client->sendPingbackToEndpoint($endpoint, 'source', 'target');
    $this->assertFalse($response);
  }

  public function testInvalidXMLResponse() {
    $endpoint = 'http://pingback-endpoint.example/invalid-xmlrpc';
    $response = $this->client->sendPingbackToEndpoint($endpoint, 'source', 'target');
    $this->assertFalse($response);
  }

  public function testInvalidBodyResponse() {
    $endpoint = 'http://pingback-endpoint.example/invalid-body';
    $response = $this->client->sendPingbackToEndpoint($endpoint, 'source', 'target');
    $this->assertFalse($response);
  }

  public function testInvalidRequest() {
    $endpoint = 'http://pingback-endpoint.example/invalid-request';
    $response = $this->client->sendPingbackToEndpoint($endpoint, 'source', 'target');
    $this->assertFalse($response);
  }

  public function testEmptyBodyResponse() {
    $endpoint = 'http://pingback-endpoint.example/empty-body';
    $response = $this->client->sendPingbackToEndpoint($endpoint, 'source', 'target');
    $this->assertTrue($response);
  }

  public function testValidResponse() {
    $endpoint = 'http://pingback-endpoint.example/valid-response';
    $response = $this->client->sendPingbackToEndpoint($endpoint, 'source', 'target');
    $this->assertTrue($response);
  }

  public function testSendPingbackNoEndpoint() {
    $target = 'http://pingback-target.example/no-endpoint.html';
    $result = $this->client->sendPingback('http://source.example.com/', $target);
    $this->assertFalse($result);
  }

  public function testSendPingbackHasValidEndpoint() {
    $target = 'http://pingback-target.example/has-valid-endpoint.html';
    $result = $this->client->sendPingback('http://source.example.com/', $target);
    $this->assertTrue($result);
  }

  public function testSendPingbackHasErroringEndpoint() {
    $target = 'http://pingback-target.example/has-erroring-endpoint.html';
    $result = $this->client->sendPingback('http://source.example.com/', $target);
    $this->assertFalse($result);
  }
}
