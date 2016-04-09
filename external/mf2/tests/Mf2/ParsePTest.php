<?php

/**
 * Tests of the parsing methods within mf2\Parser
 */

namespace Mf2\Parser\Test;

use Mf2;
use Mf2\Parser;
use PHPUnit_Framework_TestCase;


class ParsePTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		date_default_timezone_set('Europe/London');
	}

	/**
	 * @group parseP
	 */
	public function testParsePHandlesInnerText() {
		$input = '<div class="h-card"><p class="p-name">Example User</p></div>';
		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertArrayHasKey('name', $output['items'][0]['properties']);
		$this->assertEquals('Example User', $output['items'][0]['properties']['name'][0]);
	}

	/**
	 * @group parseP
	 */
	public function testParsePHandlesImg() {
		$input = '<div class="h-card"><img class="p-name" alt="Example User"></div>';
		$parser = new Parser($input);
		$output = $parser->parse();


		$this->assertArrayHasKey('name', $output['items'][0]['properties']);
		$this->assertEquals('Example User', $output['items'][0]['properties']['name'][0]);
	}

	/**
	 * @group parseP
	 */
	public function testParsePHandlesAbbr() {
		$input = '<div class="h-card h-person"><abbr class="p-name" title="Example User">@example</abbr></div>';
		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertArrayHasKey('name', $output['items'][0]['properties']);
		$this->assertEquals('Example User', $output['items'][0]['properties']['name'][0]);
	}

	/**
	 * @group parseP
	 */
	public function testParsePHandlesData() {
		$input = '<div class="h-card"><data class="p-name" value="Example User"></data></div>';
		$parser = new Parser($input);
		$output = $parser->parse();


		$this->assertArrayHasKey('name', $output['items'][0]['properties']);
		$this->assertEquals('Example User', $output['items'][0]['properties']['name'][0]);
	}

	/**
	 * @group parseP
	 */
	public function testParsePReturnsEmptyStringForBrHr() {
		$input = '<div class="h-card"><br class="p-name"/></div><div class="h-card"><hr class="p-name"/></div>';
		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertArrayHasKey('name', $output['items'][0]['properties']);
		$this->assertEquals('', $output['items'][0]['properties']['name'][0]);
		$this->assertEquals('', $output['items'][0]['properties']['name'][0]);
	}
	
	public function testParsesInputValue() {
		$input = '<span class="h-card"><input class="u-url" value="http://example.com" /></span>';
		$result = Mf2\parse($input);
		$this->assertEquals('http://example.com', $result['items'][0]['properties']['url'][0]);
	}

	/**
	 * @see https://github.com/indieweb/php-mf2/issues/53
	 * @see http://microformats.org/wiki/microformats2-parsing#parsing_an_e-_property
	 */
	public function testConvertsNestedImgElementToAltOrSrc() {
		$input = <<<EOT
<div class="h-entry">
	<p class="p-name">The day I saw a <img alt="five legged elephant" src="/photos/five-legged-elephant.jpg" /></p>
	<p class="p-summary">Blah blah <img src="/photos/five-legged-elephant.jpg" /></p>
</div>
EOT;
		$result = Mf2\parse($input, 'http://waterpigs.co.uk/articles/five-legged-elephant');
		$this->assertEquals('The day I saw a five legged elephant', $result['items'][0]['properties']['name'][0]);
		$this->assertEquals('Blah blah http://waterpigs.co.uk/photos/five-legged-elephant.jpg', $result['items'][0]['properties']['summary'][0]);
	}

	/**
	 * @see https://github.com/indieweb/php-mf2/issues/69
	 */
	public function testBrWhitespaceIssue69() {
		$input = '<div class="h-card"><p class="p-adr"><span class="p-street-address">Street Name 9</span><br/><span class="p-locality">12345 NY, USA</span></p></div>';
		$result = Mf2\parse($input);

		$this->assertEquals('Street Name 9' . "\n" . '12345 NY, USA', $result['items'][0]['properties']['adr'][0]);
		$this->assertEquals('Street Name 9', $result['items'][0]['properties']['street-address'][0]);
		$this->assertEquals('12345 NY, USA', $result['items'][0]['properties']['locality'][0]);
		$this->assertEquals('Street Name 9' . "\n" . '12345 NY, USA', $result['items'][0]['properties']['name'][0]);
	}

}
