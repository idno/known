<?php

    namespace Tests\Pages {

        class RSSTest extends \Tests\KnownTestCase {

            function testValid()
            {
                $output = [];
                exec("curl -L --silent '".\Idno\Core\Idno::site()->config()->url."?_t=rss' | xmllint --noout - 2>&1", $output);
                
                if (!empty($output)) {
                    var_export($output);
                }
                
                $this->assertTrue(empty($output));
            }

        }

    }