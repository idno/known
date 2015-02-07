<?php

    namespace Tests {

        class HomepageTest extends \PHPUnit_Framework_TestCase {

            function testHomepageLoads() {

                // Get the rendered homepage
                $contents = file_get_contents(\Idno\Core\site()->config()->url); 

                // Make sure it's not empty
                $this->assertNotEmpty($contents);

            }

        }

    }