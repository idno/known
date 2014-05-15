<?php

    namespace Tests {

        class Homepage extends \PHPUnit_Framework_TestCase {

            function testHomepageLoads() {

                // Load Known framework
                require_once(dirname(dirname(__FILE__)) . '/known/start.php');

                // Get the rendered homepage
                $contents = file_get_contents(\known\Core\site()->config()->url);

                // Make sure it's not empty
                $this->assertNotEmpty($contents);

            }

        }

    }