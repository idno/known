<?php

    namespace Tests\Pages {

        use Idno\Core\Idno;

        class HomepageTest extends KnownTestCase {

            public function testHomepageLoads()
            {
                // Get the rendered homepage
                $contents = file_get_contents(\Idno\Core\Idno::site()->config()->url);

                // Make sure it's not empty
                $this->assertNotEmpty($contents);

                // Make sure it's actually Known we're talking to
                $this->assertContains('X-Powered-By: https://withknown.com', $http_response_header);

            }

            /**
             * Test that in single-user mode, mentions of the homepage
             * (with and without a slash) are handed off to the user
             * profile page.
             */
            public function testWebmentionContentSingleUser()
            {
                Idno::site()->config()->single_user = true;

                $user = $this->user();
                $mockPage = $this->getMockBuilder('Idno\Common\Page')
                          ->getMock();
                $mockPage
                    ->method('webmentionContent')
                    ->willReturn(true);

                Idno::site()->hijackPageHandler('/profile/([^\/]+)/?', $mockPage);

                $source = 'http://foo.bar/mention';
                $target = Idno::site()->config()->getDisplayURL();

                $sourceContent = <<<EOD
<div class="h-entry">
  <a class="p-author h-card" href="http://foo.bar">Foo Bar</a>
  <span class="p-name e-content">test mention of $target</span>
</div>
EOD;
                $sourceMf2 = (new \Mf2\Parser($sourceContent, $source))->parse();
                $sourceResp = ['response' => 200, 'content' => $sourceContent];

                $homepage = new \Idno\Pages\Homepage();
                $homepage->webmentionContent($source, $target, $sourceResp, $sourceMf2);

                $mockPage
                    ->method('webmentionContent')
                    ->assertCalledOnceWith($source, $target, $sourceResp, $sourceMf2);
            }

            function testWebmentionContent_MultiUser()
            {

            }

        }

    }