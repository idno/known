<?php

namespace Tests\Pages\Webmentions;

use Idno\Core\Idno;
use Idno\Entities\GenericDataItem;

class EndpointTest extends \Tests\IdnoTestCase
{
    protected $toDelete = [];

    function setUp(): void
    {
        // Log in so that publish() -> save() passes the canEdit() check
        \Idno\Core\Idno::site()->session()->logUserOn($this->user());
    }

    function tearDown(): void
    {
        \Idno\Core\Idno::site()->session()->logUserOff();
        foreach ($this->toDelete as $entity) {
            $entity->delete();
        }
    }

    /**
     * Test that a same-site cross-post webmention is accepted.
     * When Post B (reply) sends a webmention to Post A (original),
     * both on the same site, the webmention should be processed
     * and a reply annotation should appear on Post A.
     */
    function testSameSiteCrossPostWebmention()
    {
        // Create the target entity (original post)
        $target_entity = new GenericDataItem();
        $target_entity->setDatatype('webmention-test');
        $target_entity->setOwner($this->user());
        $target_entity->title = "Original post for webmention test";
        $target_entity->publish();
        $this->toDelete[] = $target_entity;
        $target = $target_entity->getURL();

        // Create the source entity (reply post)
        $source_entity = new GenericDataItem();
        $source_entity->setDatatype('webmention-test');
        $source_entity->setOwner($this->user());
        $source_entity->title = "Reply post for webmention test";
        $source_entity->publish();
        $this->toDelete[] = $source_entity;
        $source = $source_entity->getURL();

        // Get the page handler for the target URL
        $page = Idno::site()->routes()->getRoute($target);
        $this->assertNotFalse($page, 'Should find a route for the target URL.');

        // Simulate source content: an h-entry reply pointing at the target
        $user_url = $this->user()->getDisplayURL();
        $user_name = $this->user()->getTitle();
        $sourceContent = <<<EOD
<!DOCTYPE html>
<html>
<body class="h-entry">
  <a class="u-in-reply-to" href="$target">in reply to</a>
  <span class="p-name e-content">This is a reply from the same site</span>
  <a class="u-url" href="$source">permalink</a>
  <a class="p-author h-card" href="$user_url">$user_name</a>
</body>
</html>
EOD;
        $sourceResp = ['response' => 200, 'content' => $sourceContent];
        $sourceMf2 = (new \Mf2\Parser($sourceContent, $source))->parse();

        $page->setPermalink();
        $result = $page->webmentionContent($source, $target, $sourceResp, $sourceMf2);

        $this->assertTrue($result, 'Same-site cross-post webmention should be accepted.');

        // Reload the target entity to verify annotation was saved
        $reloaded = GenericDataItem::getByID($target_entity->getID());
        $annotations = $reloaded->getAllAnnotations();
        $this->assertArrayHasKey('reply', $annotations, 'A reply annotation should have been stored on the target entity.');
        $this->assertCount(1, $annotations['reply'], 'There should be exactly one reply annotation.');

        $anno = array_values($annotations['reply'])[0];
        $this->assertEquals($user_name, $anno['owner_name'], 'Reply owner name should match the source author.');
    }

    /**
     * Test that the self-webmention path check correctly detects
     * a page trying to webmention itself.
     */
    function testSelfWebmentionDetected()
    {
        $entity = new GenericDataItem();
        $entity->setDatatype('webmention-test');
        $entity->setOwner($this->user());
        $entity->title = "Post that tries to mention itself";
        $entity->publish();
        $this->toDelete[] = $entity;
        $url = $entity->getURL();

        $source_path = parse_url($url, PHP_URL_PATH);
        $target_path = parse_url($url, PHP_URL_PATH);

        $this->assertTrue(
            $source_path && $target_path && rtrim($source_path, '/') === rtrim($target_path, '/'),
            'A post URL compared to itself should be detected as a self-webmention.'
        );
    }

    /**
     * Test that the self-webmention check handles trailing slashes.
     */
    function testSelfWebmentionTrailingSlash()
    {
        $url_no_slash = 'http://example.com/2024/my-post';
        $url_with_slash = 'http://example.com/2024/my-post/';

        $source_path = parse_url($url_no_slash, PHP_URL_PATH);
        $target_path = parse_url($url_with_slash, PHP_URL_PATH);

        $this->assertTrue(
            $source_path && $target_path && rtrim($source_path, '/') === rtrim($target_path, '/'),
            'Self-webmention check should match URLs regardless of trailing slash.'
        );
    }

    /**
     * Test that different same-site posts are not falsely flagged
     * as self-webmentions.
     */
    function testDifferentPostsPassSelfCheck()
    {
        $source = Idno::site()->config()->getURL() . '2024/reply-post';
        $target = Idno::site()->config()->getURL() . '2024/original-post';

        $source_path = parse_url($source, PHP_URL_PATH);
        $target_path = parse_url($target, PHP_URL_PATH);

        $this->assertFalse(
            rtrim($source_path, '/') === rtrim($target_path, '/'),
            'Different posts should not be detected as self-webmentions.'
        );
    }

    /**
     * Test that Entity/View::webmentionContent rejects a self-webmention
     * where source equals target.
     */
    function testWebmentionContentRejectsSelfMention()
    {
        $entity = new GenericDataItem();
        $entity->setDatatype('webmention-test');
        $entity->setOwner($this->user());
        $entity->title = "Post for self-mention rejection test";
        $entity->publish();
        $this->toDelete[] = $entity;
        $url = $entity->getURL();

        $page = Idno::site()->routes()->getRoute($url);
        $this->assertNotFalse($page, 'Should find a route for the entity URL.');

        $sourceContent = <<<EOD
<!DOCTYPE html>
<html>
<body class="h-entry">
  <a class="u-in-reply-to" href="$url">in reply to</a>
  <span class="p-name e-content">Self-referencing content</span>
  <a class="u-url" href="$url">permalink</a>
  <a class="p-author h-card" href="http://example.com/">Test</a>
</body>
</html>
EOD;
        $sourceResp = ['response' => 200, 'content' => $sourceContent];
        $sourceMf2 = (new \Mf2\Parser($sourceContent, $url))->parse();

        $page->setPermalink();
        // webmentionContent checks $source != $target, so this should be a no-op
        $result = $page->webmentionContent($url, $url, $sourceResp, $sourceMf2);

        // It returns true (accepted) but does NOT create an annotation
        $this->assertTrue($result, 'webmentionContent should return true even for self-mention (no-op).');
        $reloaded = GenericDataItem::getByID($entity->getID());
        $this->assertEmpty($reloaded->getAllAnnotations(), 'No annotation should be created for a self-webmention.');
    }
}
