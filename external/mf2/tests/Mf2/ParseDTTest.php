<?php

/**
 * Tests of the parsing methods within mf2\Parser
 */

namespace Mf2\Parser\Test;

use Mf2\Parser;
use PHPUnit_Framework_TestCase;

class ParseDTTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		date_default_timezone_set('Europe/London');
	}

	// Note that value-class tests for dt-* attributes are stored elsewhere, as there are so many of the bloody things

	/**
	 * @group parseDT
	 */
	public function testParseDTHandlesImg() {
		$input = '<div class="h-card"><img class="dt-start" alt="2012-08-05T14:50"></div>';
		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertArrayHasKey('start', $output['items'][0]['properties']);
		$this->assertEquals('2012-08-05T14:50', $output['items'][0]['properties']['start'][0]);
	}

	/**
	 * @group parseDT
	 */
	public function testParseDTHandlesDataValueAttr() {
		$input = '<div class="h-card"><data class="dt-start" value="2012-08-05T14:50"></div>';
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertArrayHasKey('start', $output['items'][0]['properties']);
		$this->assertEquals('2012-08-05T14:50', $output['items'][0]['properties']['start'][0]);
	}

	/**
	 * @group parseDT
	 */
	public function testParseDTHandlesDataInnerHTML() {
		$input = '<div class="h-card"><data class="dt-start">2012-08-05T14:50</data></div>';
		$parser = new Parser($input);
		$output = $parser->parse();


		$this->assertArrayHasKey('start', $output['items'][0]['properties']);
		$this->assertEquals('2012-08-05T14:50', $output['items'][0]['properties']['start'][0]);
	}

	/**
	 * @group parseDT
	 */
	public function testParseDTHandlesAbbrValueAttr() {
		$input = '<div class="h-card"><abbr class="dt-start" title="2012-08-05T14:50"></div>';
		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertArrayHasKey('start', $output['items'][0]['properties']);
		$this->assertEquals('2012-08-05T14:50', $output['items'][0]['properties']['start'][0]);
	}

	/**
	 * @group parseDT
	 */
	public function testParseDTHandlesAbbrInnerHTML() {
		$input = '<div class="h-card"><abbr class="dt-start">2012-08-05T14:50</abbr></div>';
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertArrayHasKey('start', $output['items'][0]['properties']);
		$this->assertEquals('2012-08-05T14:50', $output['items'][0]['properties']['start'][0]);
	}

	/**
	 * @group parseDT
	 */
	public function testParseDTHandlesTimeDatetimeAttr() {
		$input = '<div class="h-card"><time class="dt-start" datetime="2012-08-05T14:50"></div>';
		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertArrayHasKey('start', $output['items'][0]['properties']);
		$this->assertEquals('2012-08-05T14:50', $output['items'][0]['properties']['start'][0]);
	}

	/**
	 * @group parseDT
	 */
	public function testParseDTHandlesTimeInnerHTML() {
		$input = '<div class="h-card"><time class="dt-start">2012-08-05T14:50</time></div>';
		$parser = new Parser($input);
		$output = $parser->parse();


		$this->assertArrayHasKey('start', $output['items'][0]['properties']);
		$this->assertEquals('2012-08-05T14:50', $output['items'][0]['properties']['start'][0]);
	}

	/**
	 * @group parseDT
	 */
	public function testParseDTHandlesInsDelDatetime() {
		$input = '<div class="h-card"><ins class="dt-start" datetime="2012-08-05T14:50"></ins><del class="dt-end" datetime="2012-08-05T18:00"></del></div>';
		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertArrayHasKey('start', $output['items'][0]['properties']);
		$this->assertArrayHasKey('end', $output['items'][0]['properties']);
		$this->assertEquals('2012-08-05T14:50', $output['items'][0]['properties']['start'][0]);
		$this->assertEquals('2012-08-05T18:00', $output['items'][0]['properties']['end'][0]);
	}

	/**
	 * @group parseDT
	 * @group valueClass
	 */
	public function testYYYY_MM_DD__HH_MM() {
		$input = '<div class="h-event"><span class="dt-start"><span class="value">2012-10-07</span> at <span class="value">21:18</span></span></div>';
		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertArrayHasKey('start', $output['items'][0]['properties']);
		$this->assertEquals('2012-10-07T21:18', $output['items'][0]['properties']['start'][0]);
	}

	/**
	 * @group parseDT
	 * @group valueClass
	 */
	public function testAbbrYYYY_MM_DD__HH_MM() {
		$input = '<div class="h-event"><span class="dt-start"><abbr class="value" title="2012-10-07">some day</a> at <span class="value">21:18</span></span></div>';
		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertArrayHasKey('start', $output['items'][0]['properties']);
		$this->assertEquals('2012-10-07T21:18', $output['items'][0]['properties']['start'][0]);
	}

	/**
	 * @group parseDT
	 * @group valueClass
	 */
	public function testYYYY_MM_DD__HHpm() {
		$input = '<div class="h-event"><span class="dt-start"><span class="value">2012-10-07</span> at <span class="value">9pm</span></span></div>';
		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertArrayHasKey('start', $output['items'][0]['properties']);
		$this->assertEquals('2012-10-07T21:00', $output['items'][0]['properties']['start'][0]);
	}
}
