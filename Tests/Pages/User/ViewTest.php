<?php

    namespace Tests\Pages\User {

        use Idno\Core\Idno;
        use Idno\Core\Event;

        class ViewTest extends \Tests\KnownTestCase {

            function testWebmentionContent()
            {
                $user = $this->user();

                $notification = false;
                Idno::site()->addEventHook('notify', function (Event $event) use (&$notification) {
                    $eventdata    = $event->data();
                    $notification = $eventdata['notification'];
                });

                $source = 'http://karenpage.com/looking-for-information-' . md5(time() . rand(0,9999));
                $target = $user->getURL();

                $sourceContent = <<<EOD
<div class="h-entry">
  <a class="p-author h-card" href="http://karenpage.com/">Karen Page</a>
  <span class="p-name e-content">
    Hey <a href="$target">You</a> I'm trying to get some information on Frank Castle
  </span>
</div>
EOD;

                $sourceMf2 = (new \Mf2\Parser($sourceContent, $source))->parse();
                $sourceResp = ['response' => 200, 'content' => $sourceContent];

                $profile = Idno::site()->getPageHandler('/profile/' . $user->getHandle());
                $profile->webmentionContent($source, $target, $sourceResp, $sourceMf2);

                $this->assertTrue($notification !== false);
                $this->assertEquals('http://karenpage.com/', $notification['actor']);

                $this->assertEquals('You were mentioned by Karen Page on karenpage.com', $notification['message']);
                $this->assertEquals('Karen Page', $notification['object']['owner_name']);
                $this->assertEquals('http://karenpage.com/', $notification['object']['owner_url']);

                // make sure second webmention for the same source does not create another notification
                $notification = false;
                $profile->webmentionContent($source, $target, $sourceResp, $sourceMf2);
                $this->assertFalse($notification);
            }

        }

    }