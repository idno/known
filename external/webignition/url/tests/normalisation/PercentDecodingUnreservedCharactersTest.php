<?php

/**
 * Check that normalisation un-encodes unreservered characters
 *  
 */
class PercentDecodingUnreservedCharactersTest extends AbstractNormalisedUrlTest {   
    
    public function testNormalisedUrlDecodesUnreservedCharacters() {      
        $alpha = 'abcdefghijklmnopqrstuvwxyz';
        $uppercaseAlpha = strtoupper($alpha);
        $digit = '0123456789';
        $otherUnreservedCharacters = '-._~';
        
        $unreservedCharacterString = $alpha.$uppercaseAlpha.$digit.$otherUnreservedCharacters;
        $unreservedCharacters = str_split($unreservedCharacterString);
        
        $sortedKeyValues = array();
        
        $keyIndex = 0;        
        foreach  ($unreservedCharacters as $unreservedCharacter) {
            $sortedKeyValues['key'.$keyIndex] = $unreservedCharacter;          
            $keyIndex++;
        }
        
        ksort($sortedKeyValues);
        
        $encodedKeyValuePairs = array();
        $decodedKeyValuePairs = array();
        
        foreach($sortedKeyValues as $key => $value) {
            $encodedKeyValuePairs[] = $key.'=%'.dechex(ord($value));            
            $decodedKeyValuePairs[] = $key.'='.$value;              
        }
      
        $encodedQueryString = implode('&', $encodedKeyValuePairs);
        $decodedQueryString = implode('&', $decodedKeyValuePairs);
     
        $url = new \webignition\NormalisedUrl\NormalisedUrl(self::SCHEME_HTTP.'://'.self::HOST.'/?'.$encodedQueryString);        
        $this->assertEquals($decodedQueryString, (string)$url->getQuery());      
    } 
}