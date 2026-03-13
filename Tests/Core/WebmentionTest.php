<?php

namespace Tests\Core {

    use Idno\Core\Webmention;

    class WebmentionTest extends \Tests\IdnoTestCase
    {

        function testAddSyndicatedReplyTargets()
        {
            // test u-syndication
            $doc = <<<EOD
<div class="h-entry">
  <span class="p-name e-content">This is a post</span>
  <a class="u-url" href="http://foo.bar/post">permalink</a>
  <a class="u-syndication" href="https://twitter.com/foobar/12345">on Twitter</a>
  <a class="u-syndication" href="https://www.facebook.com/foobar/posts/12345">on Facebook</a>
</div>
EOD;

            $result = Webmention::addSyndicatedReplyTargets('http://foo.bar/post', [], ['response' => 200, 'content' => $doc]);
            $this->assertEquals(['https://twitter.com/foobar/12345', 'https://www.facebook.com/foobar/posts/12345'], $result, 'Syndicated reply targets should be correctly extracted from post body u-syndication mf2.');

            // test rel-syndication
            $doc = <<<EOD
<head>
  <link rel="syndication" href="https://twitter.com/foobar/12345" />
  <link rel="syndication" href="https://www.facebook.com/foobar/posts/12345" />
</head>
<body>
  <div class="h-entry">
    <span class="p-name e-content">This is a post</span>
    <a class="u-url" href="http://foo.bar/post">permalink</a>
  </div>
</body>
EOD;

            $result = Webmention::addSyndicatedReplyTargets('http://foo.bar/post', [], ['response' => 200, 'content' => $doc]);
            $this->assertEquals(['https://twitter.com/foobar/12345', 'https://www.facebook.com/foobar/posts/12345'], $result, 'Syndicated reply targets should be correctly extracted from post body rel-syndication mf2.');
        }

        /**
         * Test that findRepresentativeHEntry finds an h-entry nested inside an h-feed.
         */
        function testFindHEntryInsideHFeed()
        {
            $doc = <<<EOD
<div class="h-feed">
  <div class="h-entry">
    <span class="p-name e-content">Test post</span>
    <a class="u-url" href="http://example.com/post/1">permalink</a>
    <a class="u-in-reply-to" href="http://example.com/target">target</a>
    <a class="p-author h-card" href="http://example.com/">Author</a>
  </div>
</div>
EOD;
            $mf2 = (new \Mf2\Parser($doc, 'http://example.com/post/1'))->parse();
            $result = Webmention::findRepresentativeHEntry($mf2, 'http://example.com/post/1');

            $this->assertNotFalse($result, 'Should find h-entry nested inside h-feed');
            $this->assertContains('h-entry', $result['type']);
        }

        /**
         * Test that findRepresentativeHEntry selects the correct h-entry by URL
         * when multiple h-entries exist inside an h-feed.
         */
        function testFindHEntryByUrlInsideHFeed()
        {
            $doc = <<<EOD
<div class="h-feed">
  <div class="h-entry">
    <span class="p-name e-content">First post</span>
    <a class="u-url" href="http://example.com/post/1">permalink</a>
  </div>
  <div class="h-entry">
    <span class="p-name e-content">Second post</span>
    <a class="u-url" href="http://example.com/post/2">permalink</a>
  </div>
</div>
EOD;
            $mf2 = (new \Mf2\Parser($doc, 'http://example.com/post/2'))->parse();
            $result = Webmention::findRepresentativeHEntry($mf2, 'http://example.com/post/2');

            $this->assertNotFalse($result, 'Should find correct h-entry by URL inside h-feed');
            $this->assertContains('http://example.com/post/2', $result['properties']['url']);
        }

        /**
         * Test that findRepresentativeHEntry still works with bare top-level h-entry (no h-feed).
         */
        function testFindHEntryTopLevel()
        {
            $doc = <<<EOD
<div class="h-entry">
  <span class="p-name e-content">A simple post</span>
  <a class="u-url" href="http://example.com/post/1">permalink</a>
</div>
EOD;
            $mf2 = (new \Mf2\Parser($doc, 'http://example.com/post/1'))->parse();
            $result = Webmention::findRepresentativeHEntry($mf2, 'http://example.com/post/1');

            $this->assertNotFalse($result, 'Should still find top-level h-entry without h-feed');
            $this->assertContains('h-entry', $result['type']);
        }

    }

}

