<?php

namespace Tests\Pages\User {

    use Idno\Core\Idno;

    class ViewTest extends \Tests\KnownTestCase
    {
        function testWebmentionContent()
        {
            $user = $this->user();

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
            $result = $profile->webmentionContent($source, $target, $sourceResp, $sourceMf2);

            $this->assertTrue($result, 'webmentionContent should return true for a valid mention.');
        }
    }
}
