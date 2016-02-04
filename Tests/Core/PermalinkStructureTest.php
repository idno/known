<?php

namespace Tests\Core {

    class PermalinkStructureTest extends \Tests\KnownTestCase {

        private function createEntry()
        {
            $rnd = rand(0,9999).'-'.time();
            $entity = new \IdnoPlugins\Text\Entry();
            $entity->setOwner($this->user());
            $entity->title = "The Title $rnd";
            $entity->body = 'Unlikely to be present elsewhere in the post template: hamstring baseball duckbill firecracker';
            $entity->save(true);
            return $entity;
        }

        public function testPermalinks()
        {
            $entity = $this->createEntry();
            $base = \Idno\Core\Idno::site()->config()->getDisplayUrl();
            $year = strftime('%Y', $entity->created);
            $month = strftime('%m', $entity->created);
            $day = strftime('%d', $entity->created);
            $slug = $entity->getSlug();

            // default
            \Idno\Core\Idno::site()->config()->permalink_structure = null;
            \Idno\Core\Idno::site()->config()->save();
            $this->assertEquals('/:year/:slug', \Idno\Core\Idno::site()->config()->getPermalinkStructure());
            $this->assertEquals("$base$year/$slug", $entity->getURL());
            $contents = file_get_contents($entity->getURL());
            $this->assertContains('hamstring baseball duckbill firecracker', $contents);

            // /year/month/slug
            \Idno\Core\Idno::site()->config()->permalink_structure = '/:year/:month/:slug';
            \Idno\Core\Idno::site()->config()->save();
            $this->assertEquals("$base$year/$month/$slug", $entity->getURL());
            $contents = file_get_contents($entity->getURL());
            $this->assertContains('hamstring baseball duckbill firecracker', $contents);


            $entity->delete();
        }
    }

}
