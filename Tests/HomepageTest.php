<?php

    namespace Tests {

        class HomepageTest extends KnownTestCase {

            function testHomepageLoads() {
                
                // Get the rendered homepage
                $contents = file_get_contents(\Idno\Core\site()->config()->url); 
                                
                // Make sure it's not empty
                $this->assertNotEmpty($contents);

                // Make sure it's actually Known we're talking to                
                $this->assertContains('X-Powered-By: https://withknown.com', $http_response_header);
                
            }

        }

    }