<?php

    namespace Tests\Core {

        use Idno\Core\Webmention;

        class WebmentionTest extends \Tests\KnownTestCase {

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
                $this->assertEquals(['https://twitter.com/foobar/12345', 'https://www.facebook.com/foobar/posts/12345'], $result);

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
                $this->assertEquals(['https://twitter.com/foobar/12345', 'https://www.facebook.com/foobar/posts/12345'], $result);
            }

        }

    }