<?php

namespace Tests\Pages {

    class RSSTest extends \Tests\KnownTestCase
    {

        function testFeedLoadsAndIsValid()
        {
            $output = [];
            exec("curl -L --silent '".\Idno\Core\Idno::site()->config()->getDisplayURL()."?_t=rss' | xmllint --noout - 2>&1", $output);

            if (!empty($output)) {
                var_export($output);

                // Hack to handle travis' old build environment
                foreach ($output as $k => $v) {
                    if (strpos($v, 'Warning: program compiled against libxml')!==false) {
                        unset($output[$k]);
                    }
                }
            }

            $this->assertTrue(empty($output), 'Loading the feed should return the feed contents. If this is failing, you may need to set KNOWN_DOMAIN.');
        }

    }

}

