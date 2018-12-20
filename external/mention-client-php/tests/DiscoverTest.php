<?php
class DiscoverTest extends PHPUnit_Framework_TestCase {

  public $client;

  public function setUp() {
    $this->client = new IndieWeb\MentionClientTest(false, 'empty');
  }

  public function testDiscoverWebmentionEndpoint() {
  	$headers = "HTTP/1.1 200 OK\r
Link: <http://aaronparecki.com/webmention.php>; rel=\"webmention\"\r
Link: <http://aaronparecki.com/>; rel=\"me\"\r
";

    $target = 'http://example.com/';
    $this->client->c('headers', $target, IndieWeb\MentionClientTest::_parse_headers($headers));
    $endpoint = $this->client->discoverWebmentionEndpoint($target);
    $this->assertEquals('http://aaronparecki.com/webmention.php', $endpoint);
  }

  public function testDiscoverPingbackEndpoint() {
  	$headers = "HTTP/1.1 200 OK\r
X-Pingback: http://pingback.me/webmention?forward=http%3A%2F%2Faaronparecki.com%2Fwebmention.php\r
Link: <http://aaronparecki.com/>; rel=\"me\"\r
";

    $target = 'http://example.com/';
    $this->client->c('headers', $target, IndieWeb\MentionClientTest::_parse_headers($headers));
    $endpoint = $this->client->discoverPingbackEndpoint($target);
    $this->assertEquals('http://pingback.me/webmention?forward=http%3A%2F%2Faaronparecki.com%2Fwebmention.php', $endpoint);
  }

  public function testDiscoverWebmentionEndpointInHeader() {
    $target = 'http://target.example.com/header.html';
    $endpoint = $this->client->discoverWebmentionEndpoint($target);
    $this->assertEquals('http://webmention-endpoint.example/queued-response', $endpoint);
  }

  public function testDiscoverPingbackEndpointInHeader() {
    $target = 'http://target.example.com/header.html';
    $endpoint = $this->client->discoverPingbackEndpoint($target);
    $this->assertEquals('http://pingback-endpoint.example/valid-response', $endpoint);
  }

  public function testDiscoverWebmentionEndpointInBodyLink() {
    $target = 'http://target.example.com/body-link.html';
    $endpoint = $this->client->discoverWebmentionEndpoint($target);
    $this->assertEquals('http://webmention.example/webmention', $endpoint);

    $target = 'http://target.example.com/body-link-org.html';
    $endpoint = $this->client->discoverWebmentionEndpoint($target);
    $this->assertEquals('http://webmention.example/webmention', $endpoint);

    $target = 'http://target.example.com/body-link-org2.html';
    $endpoint = $this->client->discoverWebmentionEndpoint($target);
    $this->assertEquals('http://webmention.example/webmention', $endpoint);
  }

  public function testDiscoverPingbackEndpointInBodyLink() {
    $target = 'http://target.example.com/body-link.html';
    $endpoint = $this->client->discoverPingbackEndpoint($target);
    $this->assertEquals('http://webmention.example/pingback', $endpoint);
  }

  public function testDiscoverPingbackEndpointInBodyWithoutMf2() {
    $target = 'http://target.example.com/body-link.html';
    $this->client->usemf2 = false;
    $endpoint = $this->client->discoverPingbackEndpoint($target);
    $this->assertEquals('http://webmention.example/pingback', $endpoint);
  }

  public function testDiscoverWebmentionEndpointInBodyA() {
    $target = 'http://target.example.com/body-a.html';
    $endpoint = $this->client->discoverWebmentionEndpoint($target);
    $this->assertEquals('http://webmention.example/webmention', $endpoint);
  }

  public function testDiscoverWebmentionEndpointInBodyAWithoutMf2() {
    $target = 'http://target.example.com/body-a.html';
    $this->client->usemf2 = false;
    $endpoint = $this->client->discoverWebmentionEndpoint($target);
    $this->assertEquals('http://webmention.example/webmention', $endpoint);
  }

  public function testShouldNotDiscoverWebmentionEndpointInBodyComment() {
    $target = 'http://target.example.com/false-endpoint-in-comment.html';
    $endpoint = $this->client->discoverWebmentionEndpoint($target);
    $this->assertEquals('http://webmention.example/correct', $endpoint);
  }

  public function testDiscoverWebmentionEndpointInDocumentOrder1() {
    $target = 'http://target.example.com/document-order-1.html';
    $endpoint = $this->client->discoverWebmentionEndpoint($target);
    $this->assertEquals('http://webmention.example/link', $endpoint);
  }

  public function testDiscoverWebmentionEndpointInDocumentOrder2() {
    $target = 'http://target.example.com/document-order-2.html';
    $endpoint = $this->client->discoverWebmentionEndpoint($target);
    $this->assertEquals('http://webmention.example/a', $endpoint);
  }

  public function testDiscoverWebmentionEndpointIsEmptyString() {
    $target = 'http://target.example.com/empty-string.html';
    $endpoint = $this->client->discoverWebmentionEndpoint($target);
    $this->assertEquals('http://target.example.com/empty-string.html', $endpoint);
  }

  public function testDiscoverWebmentionEndpointIsPathRelative() {
    $target = 'http://target.example.com/relative/path-relative-endpoint.html';
    $endpoint = $this->client->discoverWebmentionEndpoint($target);
    $this->assertEquals('http://target.example.com/relative/relative', $endpoint);
  }

  public function testDiscoverEndpointAfterRedirected() {
    $target = 'http://target.example.com/redirect.html';
    $endpoint = $this->client->discoverWebmentionEndpoint($target);
    $this->assertEquals('http://target.example.com/relative/webmention', $endpoint);
  }

  public function testDiscoverWebmentionEndpointInWebmentionRocksTest1() {
    $target = "http://target.example.com/webmention-rocks-test-1.html";
    $endpoint = $this->client->discoverWebmentionEndpoint($target);
    $this->assertEquals("http://target.example.com/test/1/webmention", $endpoint);
  }

  public function testDiscoverWebmentionEndpointInWebmentionRocksTest2() {
    $target = "http://target.example.com/webmention-rocks-test-2.html";
    $endpoint = $this->client->discoverWebmentionEndpoint($target);
    $this->assertEquals("https://webmention.rocks/test/2/webmention", $endpoint);
  }

  public function testDiscoverWebmentionEndpointInWebmentionRocksTest3() {
    $target = "http://target.example.com/webmention-rocks-test-3.html";
    $endpoint = $this->client->discoverWebmentionEndpoint($target);
    $this->assertEquals("http://target.example.com/test/3/webmention", $endpoint);
  }

  public function testDiscoverWebmentionEndpointInWebmentionRocksTest4() {
    $target = "http://target.example.com/webmention-rocks-test-4.html";
    $endpoint = $this->client->discoverWebmentionEndpoint($target);
    $this->assertEquals("https://webmention.rocks/test/4/webmention", $endpoint);
  }

  public function testDiscoverWebmentionEndpointInWebmentionRocksTest5() {
    $target = "http://target.example.com/webmention-rocks-test-5.html";
    $endpoint = $this->client->discoverWebmentionEndpoint($target);
    $this->assertEquals("http://target.example.com/test/5/webmention", $endpoint);
  }

  public function testDiscoverWebmentionEndpointInWebmentionRocksTest6() {
    $target = "http://target.example.com/webmention-rocks-test-6.html";
    $endpoint = $this->client->discoverWebmentionEndpoint($target);
    $this->assertEquals("https://webmention.rocks/test/6/webmention", $endpoint);
  }

  public function testDiscoverWebmentionEndpointInWebmentionRocksTest7() {
    $target = "http://target.example.com/webmention-rocks-test-7.html";
    $endpoint = $this->client->discoverWebmentionEndpoint($target);
    $this->assertEquals("https://webmention.rocks/test/7/webmention", $endpoint);
  }

  public function testDiscoverWebmentionEndpointInWebmentionRocksTest8() {
    $target = "http://target.example.com/webmention-rocks-test-8.html";
    $endpoint = $this->client->discoverWebmentionEndpoint($target);
    $this->assertEquals("https://webmention.rocks/test/8/webmention", $endpoint);
  }

}
