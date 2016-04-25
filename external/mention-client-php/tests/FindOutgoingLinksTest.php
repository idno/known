<?php
class findOutgoingLinksTest extends PHPUnit_Framework_TestCase {

  public function testFindLinkInATag() {
    $html = '<p><a href="http://example.com/">example</a></p>';
    $links = IndieWeb\MentionClientTest::findOutgoingLinks($html);
    $this->assertEquals(array('http://example.com/'), $links);
  }

  public function testFindLinkInHEntryContent() {
    $html = '<div class="h-entry"><div class="e-content"><a href="http://example.com/">example</a></div></div>';
    $mf2 = Mf2\parse($html, 'http://source.example.net/');
    $links = IndieWeb\MentionClientTest::findOutgoingLinks($mf2);
    $this->assertEquals(array('http://example.com/'), $links);

    $html = '<div class="h-entry"><div class="p-content"><a href="http://example.com/">http://example.com/</a></div></div>';
    $mf2 = Mf2\parse($html, 'http://source.example.net/');
    $links = IndieWeb\MentionClientTest::findOutgoingLinks($mf2);
    $this->assertEquals(array('http://example.com/'), $links);
  }

  public function testFindLinkInHEntryProperty() {
    $html = '<div class="h-entry"><a href="http://example.com/" class="u-category">#example</a></div>';
    $mf2 = Mf2\parse($html, 'http://source.example.net/');
    $links = IndieWeb\MentionClientTest::findOutgoingLinks($mf2);
    $this->assertEquals(array('http://example.com/'), $links);
  }

  public function testFindLinkInNestedHEntryProperty() {
    $html = '<div class="h-entry"><div class="p-author h-card"><a href="http://example.com/" class="u-url">#example</a></div></div>';
    $mf2 = Mf2\parse($html, 'http://source.example.net/');
    $links = IndieWeb\MentionClientTest::findOutgoingLinks($mf2);
    $this->assertEquals(array('http://example.com/'), $links);
  }

  public function testFindLinksInProperties() {
    $input = json_decode('{
      "type": [
        "h-entry"
      ],
      "properties": {
        "author": [
          {
            "type": [
              "h-card"
            ],
            "properties": {
              "url": [
                "http://example.com/"
              ],
              "name": [
                "example"
              ]
            },
            "value": "example"
          }
        ],
        "name": [
          "#example"
        ],
        "url": [
          "http://source.example.net/"
        ]
      }
    }');
    $links = IndieWeb\MentionClientTest::findLinksInJSON($input);
    $this->assertEquals(array('http://example.com/','http://source.example.net/'), $links);
  }

  public function testFindNoLinksInHEntry() {
    $html = '<div class="h-entry"><div class="p-author">example</div></div>';
    $mf2 = Mf2\parse($html, 'http://source.example.net/');
    $links = IndieWeb\MentionClientTest::findOutgoingLinks($mf2);
    $this->assertEquals(array(), $links);
  }

  public function testFindNoLinksInHTML() {
    $html = '<div><p>Hello World</p></div>';
    $mf2 = Mf2\parse($html, 'http://source.example.net/');
    $links = IndieWeb\MentionClientTest::findOutgoingLinks($mf2);
    $this->assertEquals(array(), $links);
  }

}
