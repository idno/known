<?php
/**
 * Tests of the parsing methods within mf2\Parser
 */

namespace Mf2\Parser\Test;

use Mf2;
use Mf2\Parser;
use PHPUnit_Framework_TestCase;

/**
 * @todo some of these can be made into single tests with dataProviders
 */
class ParseImpliedTest extends PHPUnit_Framework_TestCase {	
	public function setUp() {
		date_default_timezone_set('Europe/London');
	}
	
	
	public function testParsesImpliedPNameFromNodeValue() {
		$input = '<span class="h-card">The Name</span>';
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertArrayHasKey('name', $output['items'][0]['properties']);
		$this->assertEquals('The Name', $output['items'][0]['properties']['name'][0]);
	}
	
	public function testParsesImpliedPNameFromImgAlt() {
		$input = '<img class="h-card" src="" alt="The Name" />';
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertArrayHasKey('name', $output['items'][0]['properties']);
		$this->assertEquals('The Name', $output['items'][0]['properties']['name'][0]);
	}
	
	public function testParsesImpliedPNameFromNestedImgAlt() {
		$input = '<div class="h-card"><img src="" alt="The Name" /></div>';
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertArrayHasKey('name', $output['items'][0]['properties']);
		$this->assertEquals('The Name', $output['items'][0]['properties']['name'][0]);
	}
	
	public function testParsesImpliedPNameFromDoublyNestedImgAlt() {
		$input = '<div class="h-card"><span><img src="" alt="The Name" /></span></div>';
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertArrayHasKey('name', $output['items'][0]['properties']);
		$this->assertEquals('The Name', $output['items'][0]['properties']['name'][0]);
	}
	
	public function testParsesImpliedUPhotoFromImgSrc() {
		$input = '<img class="h-card" src="http://example.com/img.png" alt="" />';
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertArrayHasKey('photo', $output['items'][0]['properties']);
		$this->assertEquals('http://example.com/img.png', $output['items'][0]['properties']['photo'][0]);
	}
	
	public function testParsesImpliedUPhotoFromNestedImgSrc() {
		$input = '<div class="h-card"><img src="http://example.com/img.png" alt="" /></div>';
		$parser = new Parser($input);
		$output = $parser->parse();
				
		$this->assertArrayHasKey('photo', $output['items'][0]['properties']);
		$this->assertEquals('http://example.com/img.png', $output['items'][0]['properties']['photo'][0]);
	}
	
	public function testParsesImpliedUPhotoFromDoublyNestedImgSrc() {
		$input = '<div class="h-card"><span><img src="http://example.com/img.png" alt="" /></span></div>';
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertArrayHasKey('photo', $output['items'][0]['properties']);
		$this->assertEquals('http://example.com/img.png', $output['items'][0]['properties']['photo'][0]);
	}
	
	public function testIgnoresImgIfNotOnlyChild() {
		$input = '<div class="h-card"><img src="http://example.com/img.png" /> <p>Moar text</p></div>';
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertArrayNotHasKey('photo', $output['items'][0]['properties']);
	}
	
	public function testIgnoresDoublyNestedImgIfNotOnlyDoublyNestedChild() {
		$input = '<div class="h-card"><span><img src="http://example.com/img.png" /> <p>Moar text</p></span></div>';
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertArrayNotHasKey('photo', $output['items'][0]['properties']);
	}
	
	
	public function testParsesImpliedUUrlFromAHref() {
		$input = '<a class="h-card" href="http://example.com/">Some Name</a>';
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertArrayHasKey('url', $output['items'][0]['properties']);
		$this->assertEquals('http://example.com/', $output['items'][0]['properties']['url'][0]);
	}
	
	
	public function testParsesImpliedUUrlFromNestedAHref() {
		$input = '<span class="h-card"><a href="http://example.com/">Some Name</a></span>';
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertArrayHasKey('url', $output['items'][0]['properties']);
		$this->assertEquals('http://example.com/', $output['items'][0]['properties']['url'][0]);
	}
	
	public function testMultipleImpliedHCards() {
		$input = '<span class="h-card">Frances Berriman</span>
 
<a class="h-card" href="http://benward.me">Ben Ward</a>
 
<img class="h-card" alt="Sally Ride" 
	 src="http://upload.wikimedia.org/wikipedia/commons/a/a4/Ride-s.jpg"/>
 
<a class="h-card" href="http://tantek.com">
 <img alt="Tantek Çelik" src="http://ttk.me/logo.jpg"/>
</a>';
		$expected = '{
	"rels": {},
	"items": [{
		"type": ["h-card"],
		"properties": {
			"name": ["Frances Berriman"]
		}
	},
	{
		"type": ["h-card"],
		"properties": {
			"name": ["Ben Ward"],
			"url": ["http://benward.me"]
		}
	},
	{
		"type": ["h-card"],
		"properties": {
			"name": ["Sally Ride"],
			"photo": ["http://upload.wikimedia.org/wikipedia/commons/a/a4/Ride-s.jpg"]
		}
	},
	{
		"type": ["h-card"],
		"properties": {
			"name": ["Tantek Çelik"],
			"url": ["http://tantek.com"],
			"photo": ["http://ttk.me/logo.jpg"]
		}
	}]
}';
		
		$parser = new Parser($input, '', true);
		$output = $parser->parse();
		
		$this->assertJsonStringEqualsJsonString(json_encode($output), $expected);
	}
	
	/** as per https://github.com/indieweb/php-mf2/issues/37 */
	public function testParsesImpliedNameConsistentWithPName() {
		$inner = "Name	\nand more";
		$test = '<span class="h-card"> ' . $inner .' </span><span class="h-card"><span class="p-name"> ' . $inner . ' </span></span>';
		$result = Mf2\parse($test);
		$this->assertEquals($inner, $result['items'][0]['properties']['name'][0]);
		$this->assertEquals($inner, $result['items'][1]['properties']['name'][0]);
	}
	
	
	/** @see https://github.com/indieweb/php-mf2/issues/6 */
	public function testParsesImpliedNameFromAbbrTitle() {
		$input = '<abbr class="h-card" title="Barnaby Walters">BJW</abbr>';
		$result = Mf2\parse($input);
		$this->assertEquals('Barnaby Walters', $result['items'][0]['properties']['name'][0]);
	}
}
