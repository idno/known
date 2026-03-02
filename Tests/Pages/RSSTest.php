<?php

namespace Tests\Pages {

    class RSSTest extends \Tests\IdnoTestCase
    {

        function testFeedLoadsAndIsValid()
        {
            $output = [];
            exec("curl -L --silent '".\Idno\Core\Idno::site()->config()->getDisplayURL()."?_t=rss' | xmllint --noout - 2>&1", $output);

            if (!empty($output)) {
                // Output contains unexpected content

                // Hack to handle travis' old build environment
                foreach ($output as $k => $v) {
                    if (strpos($v, 'Warning: program compiled against libxml')!==false) {
                        unset($output[$k]);
                    }
                }
            }

            $this->assertEmpty($output, 'Loading the feed should return the feed contents. If this is failing, you may need to set IDNO_DOMAIN.');
        }

    }

}

