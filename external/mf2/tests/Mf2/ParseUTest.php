<?php
/**
 * Tests of the parsing methods within mf2\Parser
 */

namespace Mf2\Parser\Test;

use Mf2;
use Mf2\Parser;
use PHPUnit_Framework_TestCase;

class ParseUTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		date_default_timezone_set('Europe/London');
	}
	
	/**
	 * @group parseU
	 */
	public function testParseUHandlesA() {
		$input = '<div class="h-card"><a class="u-url" href="http://example.com">Awesome example website</a></div>';
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertArrayHasKey('url', $output['items'][0]['properties']);
		$this->assertEquals('http://example.com', $output['items'][0]['properties']['url'][0]);
	}
	
	/**
	 * @group parseU
	 */
	public function testParseUHandlesImg() {
		$input = '<div class="h-card"><img class="u-photo" src="http://example.com/someimage.png"></div>';
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertArrayHasKey('photo', $output['items'][0]['properties']);
		$this->assertEquals('http://example.com/someimage.png', $output['items'][0]['properties']['photo'][0]);
	}
	
	/**
	 * @group parseU
	 */
	public function testParseUHandlesArea() {
		$input = '<div class="h-card"><area class="u-photo" href="http://example.com/someimage.png"></area></div>';
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertArrayHasKey('photo', $output['items'][0]['properties']);
		$this->assertEquals('http://example.com/someimage.png', $output['items'][0]['properties']['photo'][0]);
	}
	
	/**
	 * @group parseU
	 */
	public function testParseUHandlesObject() {
		$input = '<div class="h-card"><object class="u-photo" data="http://example.com/someimage.png"></object></div>';
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertArrayHasKey('photo', $output['items'][0]['properties']);
		$this->assertEquals('http://example.com/someimage.png', $output['items'][0]['properties']['photo'][0]);
	}
	
	/**
	 * @group parseU
	 */
	public function testParseUHandlesAbbr() {
		$input = '<div class="h-card"><abbr class="u-photo" title="http://example.com/someimage.png"></abbr></div>';
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertArrayHasKey('photo', $output['items'][0]['properties']);
		$this->assertEquals('http://example.com/someimage.png', $output['items'][0]['properties']['photo'][0]);
	}
	
	/**
	 * @group parseU
	 */
	public function testParseUHandlesData() {
		$input = '<div class="h-card"><data class="u-photo" value="http://example.com/someimage.png"></data></div>';
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertArrayHasKey('photo', $output['items'][0]['properties']);
		$this->assertEquals('http://example.com/someimage.png', $output['items'][0]['properties']['photo'][0]);
	}
	
	/**
	 * @group baseUrl
	 */
	public function testResolvesRelativeUrlsFromDocumentUrl() {
		$input = '<div class="h-card"><img class="u-photo" src="../image.png" /></div>';
		$parser = new Parser($input, 'http://example.com/things/more/more.html');
		$output = $parser->parse();
		
		$this->assertEquals('http://example.com/things/image.png', $output['items'][0]['properties']['photo'][0]);
	}
	
	/**
	 * @group baseUrl
	 */
	public function testResolvesRelativeUrlsFromBaseUrl() {
		$input = '<head><base href="http://example.com/things/more/andmore/" /></head><body><div class="h-card"><img class="u-photo" src="../image.png" /></div></body>';
		$parser = new Parser($input, 'http://example.com/things/more.html');
		$output = $parser->parse();
		
		$this->assertEquals('http://example.com/things/more/image.png', $output['items'][0]['properties']['photo'][0]);
	}
	
	/**
	 * @group baseUrl
	 */
	public function testResolvesRelativeUrlsInImpliedMicroformats() {
		$input = '<a class="h-card"><img src="image.png" /></a>';
		$parser = new Parser($input, 'http://example.com/things/more.html');
		$output = $parser->parse();
		
		$this->assertEquals('http://example.com/things/image.png', $output['items'][0]['properties']['photo'][0]);
	}
	
	/**
	 * @group baseUrl
	 */
	public function testResolvesRelativeBaseRelativeUrlsInImpliedMicroformats() {
		$input = '<base href="things/"/><a class="h-card"><img src="image.png" /></a>';
		$parser = new Parser($input, 'http://example.com/');
		$output = $parser->parse();
		
		$this->assertEquals('http://example.com/things/image.png', $output['items'][0]['properties']['photo'][0]);
	}
	
	/** @see https://github.com/indieweb/php-mf2/issues/33 */
	public function testParsesHrefBeforeValueClass() {
		$input = '<span class="h-card"><a class="u-url" href="http://example.com/right"><span class="value">WRONG</span></a></span>';
		$result = Mf2\parse($input);
		$this->assertEquals('http://example.com/right', $result['items'][0]['properties']['url'][0]);
	}

	/**
	 * @group parseU
	 */
	public function testParseUHandlesAudio() {
		$input = '<div class="h-entry"><audio class="u-audio" src="http://example.com/audio.mp3"></div>';
		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertArrayHasKey('audio', $output['items'][0]['properties']);
		$this->assertEquals('http://example.com/audio.mp3', $output['items'][0]['properties']['audio'][0]);
	}

	/**
	 * @group parseU
	 */
	public function testParseUHandlesVideo() {
		$input = '<div class="h-entry"><video class="u-video" src="http://example.com/video.mp4"></video></div>';
		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertArrayHasKey('video', $output['items'][0]['properties']);
		$this->assertEquals('http://example.com/video.mp4', $output['items'][0]['properties']['video'][0]);
	}


	/**
	 * @group parseU
	 */
	public function testParseUHandlesSource() {
		$input = '<div class="h-entry"><video><source class="u-video" src="http://example.com/video.mp4" type="video/mp4"><source class="u-video" src="http://example.com/video.ogg" type="video/ogg"></video></div>';
		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertArrayHasKey('video', $output['items'][0]['properties']);
		$this->assertEquals('http://example.com/video.mp4', $output['items'][0]['properties']['video'][0]);
		$this->assertEquals('http://example.com/video.ogg', $output['items'][0]['properties']['video'][1]);
	}

	/**
	 * @group parseU
	 */
	public function testParseUWithSpaces() {
		$input = '<div class="h-card"><a class="u-url" href=" http://example.com ">Awesome example website</a></div>';
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertArrayHasKey('url', $output['items'][0]['properties']);
		$this->assertEquals('http://example.com', $output['items'][0]['properties']['url'][0]);
	}
	

}
