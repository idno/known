<?php

class InputHasRelativePathOnlyTest extends PHPUnit_Framework_TestCase {   
    
    public function testRelativePathIsTransformedIntoCorrectAbsoluteUrl() {
        $deriver = new \webignition\AbsoluteUrlDeriver\AbsoluteUrlDeriver(
            'server.php?param1=value1',
            'http://www.example.com/pathOne/pathTwo/pathThree'
        );

        $this->assertEquals('http://www.example.com/pathOne/pathTwo/pathThree/server.php?param1=value1', (string)$deriver->getAbsoluteUrl());
    } 
    
    public function testAbsolutePathHasDotDotDirecoryAndSourceHasFileName() {
        $deriver = new \webignition\AbsoluteUrlDeriver\AbsoluteUrlDeriver(
            '../jquery.js',
            'http://www.example.com/pathOne/index.php'
        );

        $this->assertEquals('http://www.example.com/jquery.js', (string)$deriver->getAbsoluteUrl());
    }     
    
    public function testAbsolutePathHasDotDotDirecoryAndSourceHasDirectoryWithTrailingSlash() {
        $deriver = new \webignition\AbsoluteUrlDeriver\AbsoluteUrlDeriver(
            '../jquery.js',
            'http://www.example.com/pathOne/'
        );

        $this->assertEquals('http://www.example.com/jquery.js', (string)$deriver->getAbsoluteUrl());
    }       
    
    public function testAbsolutePathHasDotDotDirecoryAndSourceHasDirectoryWithoutTrailingSlash() {
        $deriver = new \webignition\AbsoluteUrlDeriver\AbsoluteUrlDeriver(
            '../jquery.js',
            'http://www.example.com/pathOne'
        );

        $this->assertEquals('http://www.example.com/jquery.js', (string)$deriver->getAbsoluteUrl());
    }     
    
    public function testAbsolutePathHasDotDirecoryAndSourceHasFilename() {
        $deriver = new \webignition\AbsoluteUrlDeriver\AbsoluteUrlDeriver(
            './jquery.js',
            'http://www.example.com/pathOne/index.php'
        );

        $this->assertEquals('http://www.example.com/pathOne/jquery.js', (string)$deriver->getAbsoluteUrl());
    }      
    
    public function testAbsolutePathHasDotDirecoryAndSourceHasDirectoryWithTrailingSlash() {
        $deriver = new \webignition\AbsoluteUrlDeriver\AbsoluteUrlDeriver(
            './jquery.js',
            'http://www.example.com/pathOne/'
        );

        $this->assertEquals('http://www.example.com/pathOne/jquery.js', (string)$deriver->getAbsoluteUrl());
    }      
    
    
    public function testAbsolutePathHasDotDirecoryAndSourceHasDirectoryWithoutTrailingSlash() {
        $deriver = new \webignition\AbsoluteUrlDeriver\AbsoluteUrlDeriver(
            './jquery.js',
            'http://www.example.com/pathOne'
        );

        $this->assertEquals('http://www.example.com/pathOne/jquery.js', (string)$deriver->getAbsoluteUrl());
    }      
}