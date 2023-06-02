<?php

namespace Tests\Pages\User {

    use Idno\Core\Idno;
    use Idno\Core\Event;

    class ViewTest extends \Tests\KnownTestCase
    {
        function testWebmentionContent()
        {
            $user = $this->user();

            $notification = false;
            Idno::site()->events()->addListener(
                'notify', function (Event $event) use (&$notification) {
                    $eventdata    = $event->data();
                    $notification = $eventdata['notification'];
                }
            );

            $source = 'http://karenpage.dummy/looking-for-information-' . md5(time() . rand(0, 9999));
            $target = $user->getURL();

            $sourceContent = <<<EOD
<div class="h-entry">
  <a class="p-author h-card" href="http://karenpage.dummy/">Karen Page</a>
  <span class="p-name e-content">
    Hey <a href="$target">You</a> I'm trying to get some information on Frank Castle
  </span>
</div>
EOD;

            $sourceMf2 = (new \Mf2\Parser($sourceContent, $source))->parse();
            $sourceResp = ['response' => 200, 'content' => $sourceContent];

            $profile = Idno::site()->getPageHandler('/profile/' . $user->getHandle());
            $profile->webmentionContent($source, $target, $sourceResp, $sourceMf2);

            $this->assertTrue($notification !== false, 'A notification should have been set.');
            $this->assertEquals('http://karenpage.dummy/', $notification['actor'], 'The actor on the notification should be the source URL.');

            $this->assertEquals('You were mentioned by Karen Page on karenpage.dummy', $notification['message'], 'The notification message should be set appropriately.');
            $this->assertEquals('Karen Page', $notification['object']['owner_name'], 'The notification owner name should be set appropriately.');
            $this->assertEquals('http://karenpage.dummy/', $notification['object']['owner_url'], 'The notification owner URL should be set appropriately.');

            // make sure second webmention for the same source does not create another notification
            $notification = false;
            $profile->webmentionContent($source, $target, $sourceResp, $sourceMf2);
            $this->assertFalse($notification, 'A second webmention from the same source should not create a second notification.');
        }
    }
}

