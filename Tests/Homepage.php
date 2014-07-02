<?php

    namespace Tests {

        class Homepage extends \PHPUnit_Framework_TestCase {

            function testHomepageLoads() {

                // Load Known framework
                require_once(dirname(dirname(__FILE__)) . '/Idno/start.php');

                // Get the rendered homepage
                $contents = file_get_contents(\Idno\Core\site()->config()->url);

                // Make sure it's not empty
                $this->assertNotEmpty($contents);

            }

        }

    }