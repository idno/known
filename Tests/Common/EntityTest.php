<?php

namespace Tests\Common;

use Idno\Common\Entity;
use Idno\Core\Idno;
use Idno\Core\Webservice;

class EntityTest extends \Tests\KnownTestCase {

    public function setUp()
    {
        $this->user()->notifications['email'] = 'none';
        $this->user()->save();
    }

    function tearDown()
    {
        if (isset($this->toDelete)) {
            foreach ($this->toDelete as $entity) {
                $entity->delete();
            }
        }
    }

    public function testPrepareSlug()
    {
        $entity = new Entity();
        $this->assertEquals(
            'test-a-simple-title',
            $entity->prepareSlug('Test a Simple Title'));
        $this->assertEquals(
            'true-and-i-dont-really-like-how-there-are-never',
            $entity->prepareSlug("True and I don't really like how there are never leftovers. But the recipes have been really creative, definitely different stuff (and more complicated) than I would think to cook on my own."));
        $this->assertEquals(
            'aus-base-wird-o2---dann-versuchen-wir-mal-den',
            $entity->prepareSlug('Aus BASE wird O2 - Dann versuchen wir mal, den Kunden über den Tisch zu ziehen'));
        $this->assertEquals(
            'liked-a-post-by-ben-werdm%C3%BCller',
            $entity->prepareSlug('liked a post by Ben Werdmüller'));
        $this->assertEquals(
            'the-top-1%25-of-the-top-1%25',
            $entity->prepareSlug('The Top 1% of the Top 1%'));
        $this->assertEquals(
            'voil%C3%A0-this-title-has-a-%25-sign-%D0%B4%D0%BE-%D1%81%D0%B2%D0%B8%D0%B4%D0%B0%D0%BD%D0%B8%D1%8F',
            $entity->prepareSlug('Voilà, this title has a % sign, до свидания'));
        // titles with many long words may need to be truncated mid-word
        $this->assertEquals(
            'thistitleisreallyreallylong-with',
            $entity->prepareSlug('ThisTitleIsReallyReallyLong WithVeryFewWords', 10, 32));
        // borrowed this one from @nekr0z
        $this->assertEquals(
            '%D0%B4%D0%B0-%D1%8F-%D0%B6-%D0%B4%D0%B0%D0%B2%D0%B5%D1%87%D0%B0-%D0%B2-%D1%81%D0%BF%D0%BE%D1%80%D1%82%D0%BC%D0%B0%D1%81%D1%82%D0%B5%D1%80%D0%B5-%D0%B1%D1%8B%D0%BB',
            $entity->prepareSlug('Да, я ж давеча в Спортмастере был'));
        // don't truncate in the middle of a encoded character
        $this->assertEquals(
            '%D0%B4%D0%B0-%D1%8F',
            $entity->prepareSlug('Да, я ж давеча в Спортмастере был', 10, 24));
        $this->assertEquals(
            'make-sure-spaces-are-collapsed-and-tags-are-stripped',
            $entity->prepareSlug('Make Sure    <b>Spaces</b> are  Collapsed and Tags  Are  <i>Stripped</i>'));
    }

    function testSetSlugResilient()
    {
        $unique = md5(time() . rand(0, 9999));
        $title  = "IndieWebCamp Nürnberg $unique is live!";
        $slug   = "indiewebcamp-n%C3%BCrnberg-$unique-is-live";

        $entity = new Entity();
        $entity->setSlugResilient($title);
        $this->assertEquals($slug, $entity->getSlug());
        $entity->save();
        $this->toDelete[] = $entity;

        $entity = new Entity();
        $entity->setSlugResilient($title);
        $this->assertEquals($slug . '-1', $entity->getSlug());
        $entity->save();
        $this->toDelete[] = $entity;
    }

    /**
     * Send a simple reply and make sure it gets collected.
     */
    function testAddWebmentions_SimpleReply()
    {
        $entity = new Entity();
        $entity->setOwner($this->user());
        $entity->title = "This will be the target of our webmention";
        $entity->publish();
        $this->toDelete[] = $entity;

        $target = $entity->getURL();
        $source = 'http://example.com/2015/this-is-a-reply';
        $sourceContent = <<<EOD
<!DOCTYPE html>
<html>
<body class="h-entry">
  <a class="u-in-reply-to" href="$target">in reply to</a>
  <span class="p-name e-content">This is a reply</span>
  <a class="u-url" href="http://example.com/2015/this-is-a-reply">permalink</a>
  <a class="p-author h-card" href="https://example.com/">Jane Example</a>
</body>
</html>
EOD;
        $sourceResp = ['response' => 200, 'content' => $sourceContent];
        $sourceMf2 = (new \Mf2\Parser($sourceContent, $source))->parse();
        $entity->addWebmentions($source, $target, $sourceResp, $sourceMf2);

        $this->assertArrayHasKey('reply', $entity->getAllAnnotations());
        $this->assertCount(1, $entity->getAllAnnotations()['reply']);

        $anno = array_values($entity->getAllAnnotations()['reply'])[0];

        $this->assertArrayHasKey('owner_name', $anno);
        $this->assertEquals('Jane Example', $anno['owner_name']);
        $this->assertArrayHasKey('permalink', $anno);
        $this->assertEquals($source, $anno['permalink']);
    }

    /**
     * Test some other annotation types
     */
    function testAddWebmentions_OtherTypes()
    {
        $entity = new Entity();
        $entity->setOwner($this->user());
        $entity->title = "This will be the target of our webmention";
        $entity->publish();
        $this->toDelete[] = $entity;

        $target = $entity->getURL();


        $sources = [
            'http://joe.example/this-is-a-like' => <<<EOD
<!DOCTYPE html>
<html>
<body>
<div class="h-entry">
  <p class="p-name">
    I liked this other post
    <a class="u-like-of" href="$target">on example.com</a>
  </p>
</div>
<a class="h-card" href="https://joe.example/">Joe Example</a>
</body>
</html>
EOD
            ,'http://gary.example/this-is-a-mention' => <<<EOD
<!DOCTYPE html>
<html>
<body>
<div class="h-entry">
  <p class="p-name">
    I just want to mention this post <a href="$target">on example.com</a>
  </p>
  <a class="p-author h-card" href="http://gary.example/">Gary Example</a>
  <a class="u-url" href="http://gary.example/this-is-a-mention"></a>
</div>
</body>
</html>
EOD
            ,'http://chelsea.example/chelsea-liked-a-post' => <<<EOD
<!DOCTYPE html>
<html>
<body class="h-entry">
  <p class="p-name">
    <a class="p-author h-card" href="https://chelsea.example/">Chelsea Example</a> liked a post
    <a class="u-like-of" href="$target">on example.com</a>
  </p>
</body>
</html>
EOD
        ];

        foreach ($sources as $source => $sourceContent) {
            $sourceMf2 = (new \Mf2\Parser($sourceContent, $source))->parse();
            $entity->addWebmentions($source, $target, ['response' => 200, 'content' => $sourceContent], $sourceMf2);
        }

        $this->assertArrayHasKey('like', $entity->getAllAnnotations());
        $this->assertCount(2, $entity->getAllAnnotations()['like']);

        $anno = array_values($entity->getAllAnnotations()['like'])[0];

        $this->assertArrayHasKey('owner_name', $anno);
        $this->assertEquals('Joe Example', $anno['owner_name']);
        $this->assertArrayHasKey('permalink', $anno);
        $this->assertEquals('http://joe.example/this-is-a-like', $anno['permalink']);

        $anno = array_values($entity->getAllAnnotations()['like'])[1];

        $this->assertArrayHasKey('owner_name', $anno);
        $this->assertEquals('Chelsea Example', $anno['owner_name']);
        $this->assertArrayHasKey('permalink', $anno);
        $this->assertEquals('http://chelsea.example/chelsea-liked-a-post', $anno['permalink']);

        $this->assertArrayHasKey('mention', $entity->getAllAnnotations());

        $anno = array_values($entity->getAllAnnotations()['mention'])[0];

        $this->assertArrayHasKey('owner_name', $anno);
        $this->assertEquals('Gary Example', $anno['owner_name']);
        $this->assertArrayHasKey('permalink', $anno);
        $this->assertEquals('http://gary.example/this-is-a-mention', $anno['permalink']);

    }


    /**
     * When we get a webmention where the source is a feed, make
     * sure we handle it gracefully.
     */
    function testAddWebmentions_RemoteFeed()
    {
        $entity = new Entity();
        $entity->setOwner($this->user());
        $entity->title = "This post will be the webmention target";
        $entity->publish();
        $this->toDelete[] = $entity;

        $target = $entity->getURL();
        $source = 'http://example.com/';
        $sourceContent = <<<EOD
<!DOCTYPE html>
<html>
<body>
  <div class="h-entry">
    <a class="p-author h-card" href="https://example.com/">Jane Example</a>
    <span class="p-name e-content">This is just nonsense</span>
    <a class="u-url" href="http://example.com/2015/this-is-just-nonsense">permalink</a>
  </div>
  <div class="h-entry">
    <a class="u-in-reply-to" href="$target">in reply to</a>
    <a class="p-author h-card" href="https://example.com/">Jane Example</a>
    <span class="p-name e-content">This is a reply</span>
    <a class="u-url" href="http://example.com/2015/this-is-a-reply">permalink</a>
  </div>
  <div class="h-entry">
    <a class="p-author h-card" href="https://example.com/">Jane Example</a>
    <span class="p-name e-content">This is probably really serious</span>
    <a class="u-url" href="http://example.com/2015/this-is-probably-really-serious">permalink</a>
  </div>
</body>
</html>
EOD;

        $sourceResp = ['response' => 200, 'content' => $sourceContent];
        $sourceMf2 = (new \Mf2\Parser($sourceContent, $source))->parse();
        $entity->addWebmentions($source, $target, $sourceResp, $sourceMf2);

        $this->assertEmpty($entity->getAllAnnotations());
    }

    /**
     * A particularly knotty case when we get a webmention from *our
     * own* feed. It looks valid because it includes a link, but it's
     * not really.
     */
    function testAddWebmentions_LocalFeed()
    {
        for ($i = 0 ; $i < 5 ; $i++) {
            $entity = new Entity();
            $entity->setOwner($this->user());
            $entity->title = "This post will be the webmention target";
            $entity->publish();
            $entities[] = $entity;
            $this->toDelete[] = $entity;
        }

        // take one from the middle
        $entity = $entities[2];

        $source = \Idno\Core\Idno::site()->config()->url;
        $target = $entity->getURL();

        // render the homepage feed
        $sourceResp = Webservice::get($source);
        $sourceContent = $sourceResp['content'];
        $sourceMf2 = (new \Mf2\Parser($sourceContent, $source))->parse();
        $entity->addWebmentions($source, $target, $sourceResp, $sourceMf2);

        $this->assertEmpty($entity->getAllAnnotations());
    }


}
