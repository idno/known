<?php

    namespace Tests {

        class HomepageTest extends \PHPUnit_Framework_TestCase {

            function testHomepageLoads() {

                $ch = curl_init();
curl_setopt($ch, CURLOPT_URL, \Idno\Core\site()->config()->url);
curl_setopt($ch, CURLOPT_HEADER, TRUE);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$a = curl_exec($ch);
if(preg_match('#Location: (.*)#', $a, $r))
 $l = trim($r[1]); echo $l;
                
                // Get the rendered homepage
                $contents = file_get_contents(\Idno\Core\site()->config()->url); 

                // Make sure it's not empty
                $this->assertNotEmpty($contents);

                // Make sure it's actually Known we're talking to                
                $this->assertContains('X-Powered-By: https://withknown.com', $http_response_header);
                
            }

        }

    }