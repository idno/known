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
            $entity->prepareSlug('Да, я ж давеча в Спортмастере был...'));
        $this->assertEquals(
            'make-sure-spaces-are-collapsed-and-tags-are-stripped',
            $entity->prepareSlug('Make Sure    <b>Spaces</b> are  Collapsed and Tags  Are  <i>Stripped</i>'));
    }

}