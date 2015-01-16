<?php

/**
 *  
 */
class HostTest extends AbstractRegularUrlTest {      
    
    public function testGet() {        
        $url = new \webignition\Url\Url('http://example.com/');
        $this->assertTrue($url->getHost() instanceof \webignition\Url\Host\Host);
        $this->assertEquals("example.com", (string)$url->getHost());
    }   
    
    public function testGetParts() {        
        $url = new \webignition\Url\Url('http://example.com/');
        $this->assertEquals(array(
            'example',
            'com'
        ), $url->getHost()->getParts());
    }      
    
    public function testComparison() {
        $url1 = new \webignition\Url\Url('http://example.com');
        $url2 = new \webignition\Url\Url('http://example.com');
        $url3 = new \webignition\Url\Url('http://www.example.com');
        
        $this->assertTrue($url1->getHost()->equals($url2->getHost()));
        $this->assertFalse($url1->getHost()->equals($url3->getHost()));
    }
    
    public function testEquivalence() {
        $url1 = new \webignition\Url\Url('http://example.com');
        $url2 = new \webignition\Url\Url('http://example.com');
        $url3 = new \webignition\Url\Url('http://www.example.com');
        
        $this->assertTrue($url1->getHost()->isEquivalentTo($url2->getHost()));
        $this->assertFalse($url1->getHost()->isEquivalentTo($url3->getHost()));
        
        $this->assertTrue($url1->getHost()->isEquivalentTo(
                $url3->getHost(),
                array('www')
        ));
        
        $this->assertTrue($url3->getHost()->isEquivalentTo(
                $url1 ->getHost(),
                array('www')
        ));        
    }
    
    public function testIdnEquivalence() {
        $idnUrl1 = new \webignition\Url\Url('http://econom.ía.com');
        $idnUrl2 = new \webignition\Url\Url('http://econom.xn--a-iga.com');
        
        $idnUrl3 = new \webignition\Url\Url('http://ヒキワリ.ナットウ.ニホン');
        $idnUrl4 = new \webignition\Url\Url('http://xn--nckwd5cta.xn--gckxcpg.xn--idk6a7d');

        $idnUrl5 = new \webignition\Url\Url('http://транспорт.com'); 
        $idnUrl6 = new \webignition\Url\Url('http://xn--80a0addceeeh.com'); 
        
        $this->assertTrue($idnUrl1->getHost()->isEquivalentTo($idnUrl2->getHost()));
        $this->assertTrue($idnUrl3->getHost()->isEquivalentTo($idnUrl4->getHost()));
        $this->assertTrue($idnUrl5->getHost()->isEquivalentTo($idnUrl6->getHost()));
    }
}