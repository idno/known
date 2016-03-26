<?php

    namespace Tests\Pages {

        use Idno\Core\Idno;
        use Idno\Core\Event;
        use Idno\Core\Webservice;

        class HomepageTest extends \Tests\KnownTestCase {

            function testHomepageLoads()
            {
                // Get the rendered homepage
                $contents = file_get_contents(\Idno\Core\Idno::site()->config()->url);

                // Make sure it's not empty
                $this->assertNotEmpty($contents);

                // Make sure it's actually Known we're talking to
                $this->assertContains('X-Powered-By: https://withknown.com', $http_response_header);
            }

            function test404Page()
            {
                $result = Webservice::get(Idno::site()->config()->getURL() . 'this-resource-does-not-exist');
                $this->assertEquals(404, $result['response']);
            }

            private function doWebmentionContent($source, $target)
            {
                $notification = false;
                Idno::site()->addEventHook('notify', function (Event $event) use (&$notification) {
                    $eventdata    = $event->data();
                    $notification = $eventdata['notification'];
                });

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

                return $notification;
            }


            /**
             * Test that in single-user mode, mentions of the homepage
             * are handed off to the user profile page.
             */
            function testWebmentionContentSingleUser()
            {
                Idno::site()->config->single_user = true;
                $this->admin(); // make sure there is an admin user
                // uniquify source to make sure we get the notification
                $source = 'http://foo.bar/homepage-mention-single-user-'.md5(time() . rand(0,9999));
                $target = Idno::site()->config()->getDisplayURL();
                $notification = $this->doWebmentionContent($source, $target);
                $this->assertTrue($notification !== false);
                $this->assertEquals('http://foo.bar', $notification['actor']);
                $this->assertEquals('You were mentioned by Foo Bar on foo.bar', $notification['message']);
                $this->assertEquals('Foo Bar', $notification['object']['owner_name']);
                $this->assertEquals('http://foo.bar', $notification['object']['owner_url']);
            }


            /**
             * Test that in single-user mode without a trailing slash
             * on the domain name
             */
            function testWebmentionContentSingleUserNoSlash()
            {
                Idno::site()->config->single_user = true;
                $this->admin(); // make sure there is an admin user
                $source = 'http://foo.bar/homepage-mention-single-user-no-slash-'.md5(time() . rand(0,9999));
                $target = rtrim(Idno::site()->config()->getDisplayURL(), '/');
                $notification = $this->doWebmentionContent($source, $target);
                $this->assertTrue($notification !== false);
                $this->assertEquals('http://foo.bar', $notification['actor']);
                $this->assertEquals('You were mentioned by Foo Bar on foo.bar', $notification['message']);
                $this->assertEquals('Foo Bar', $notification['object']['owner_name']);
                $this->assertEquals('http://foo.bar', $notification['object']['owner_url']);
            }

            /**
             * Make sure that we're not notifying anyone of homepage mentions
             * for multiuser sites
             */
            function testWebmentionContentMultiUser()
            {
                Idno::site()->config->single_user = false;
                $source = 'http://foo.bar/homepage-mention-multi-user-'.md5(time() . rand(0,9999));
                $target = Idno::site()->config()->getDisplayURL();
                $notification = $this->doWebmentionContent($source, $target);
                $this->assertFalse($notification);
            }

        }

    }