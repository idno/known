<?php

use webignition\NormalisedUrl\NormalisedUrl;

/**
 * Check that host normalisation normalises IDNs to the ascii variant
 *  
 */
class IdnHostNormalisationTest extends AbstractNormalisedUrlTest {   
    
    public function testNormalisedUrlIgnoresHostCase() {      
        $url1 = new NormalisedUrl('http://artesan.xn--a-iga.com/');
        $url2 = new NormalisedUrl('http://artesan.ía.com/');
        
        $this->assertEquals((string)$url1, (string)$url2);
    } 
}