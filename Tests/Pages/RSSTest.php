<?php

    namespace Tests\Pages {

        class RSSTest extends \Tests\KnownTestCase {

            function testValid()
            {
                $output = [];
                exec("curl -L --silent '".\Idno\Core\Idno::site()->config()->url."?_t=rss' | xmllint --noout - 2>&1", $output);
                
                if (!empty($output)) {
                    var_export($output);
                    
                    // Hack to handle travis' old build environment
                    foreach ($output as $k => $v) {
                        if (strpos($v, 'Warning: program compiled against libxml')!==false) {
                            unset($output[$k]);
                        }
                    }
                }
                
                $this->assertTrue(empty($output));
            }

        }

    }