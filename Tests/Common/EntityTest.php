<?php

namespace Tests\Common;

use Idno\Common\Entity;

class EntityTest extends \Tests\KnownTestCase {

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
    }

}