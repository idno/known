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
		$input = '<div class="h-event"><span class="dt-start"><abbr class="value" title="2012-10-07">some day</abbr> at <span class="value">21:18</span></span></div>';
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

	/**
	 * @group parseDT
	 * @group valueClass
	 */
	public function testYYYY_MM_DD__HH_MMpm() {
		$input = '<div class="h-event"><span class="dt-start"><span class="value">2012-10-07</span> at <span class="value">9:00pm</span></span></div>';
		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertArrayHasKey('start', $output['items'][0]['properties']);
		$this->assertEquals('2012-10-07T21:00', $output['items'][0]['properties']['start'][0]);
	}

	/**
	 * @group parseDT
	 * @group valueClass
	 */
	public function testYYYY_MM_DD__HH_MM_SSpm() {
		$input = '<div class="h-event"><span class="dt-start"><span class="value">2012-10-07</span> at <span class="value">9:00:00pm</span></span></div>';
		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertArrayHasKey('start', $output['items'][0]['properties']);
		$this->assertEquals('2012-10-07T21:00:00', $output['items'][0]['properties']['start'][0]);
	}

	/**
	 * This test name refers to the value-class used within the dt-end.
	 * @group parseDT
	 * @group valueClass
	 */
	public function testImpliedDTEndWithValueClass() {
		$input = '<div class="h-event"> <span class="dt-start"><span class="value">2014-06-04</span> at <span class="value">18:30</span> <span class="dt-end"><span class="value">19:30</span></span></span> </div>';
		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertArrayHasKey('start', $output['items'][0]['properties']);
		$this->assertArrayHasKey('end', $output['items'][0]['properties']);
		$this->assertEquals('2014-06-04T18:30', $output['items'][0]['properties']['start'][0]);
		$this->assertEquals('2014-06-04T19:30', $output['items'][0]['properties']['end'][0]);
	}

	/**
	 * This test name refers to the lack of value-class within the dt-end.
	 * @group parseDT
	 * @group valueClass
	 */
	public function testImpliedDTEndWithoutValueClass() {
		$input = '<div class="h-event"> <span class="dt-start"><span class="value">2014-06-05</span> at <span class="value">18:31</span> <span class="dt-end">19:31</span></span> </div>';

		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertArrayHasKey('start', $output['items'][0]['properties']);
		$this->assertArrayHasKey('end', $output['items'][0]['properties']);
		$this->assertEquals('2014-06-05T18:31', $output['items'][0]['properties']['start'][0]);
		$this->assertEquals('2014-06-05T19:31', $output['items'][0]['properties']['end'][0]);
	}

	/**
	 * @see https://github.com/indieweb/php-mf2/pull/46
	 * @group parseDT
	 * @group valueClass
	 */
	public function testImpliedDTEndUsingNonValueClassDTStart() {
		$input = '<div class="h-event"> <time class="dt-start">2014-06-05T18:31</time> until <span class="dt-end">19:31</span></span> </div>';

		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertArrayHasKey('start', $output['items'][0]['properties']);
		$this->assertArrayHasKey('end', $output['items'][0]['properties']);
		$this->assertEquals('2014-06-05T18:31', $output['items'][0]['properties']['start'][0]);
		$this->assertEquals('2014-06-05T19:31', $output['items'][0]['properties']['end'][0]);
	}

	/**
	 * @group parseDT
	 * @group valueClass
	 */
	public function testDTStartOnly() {
		$input = '<div class="h-event"> <span class="dt-start"><span class="value">2014-06-06</span> at <span class="value">18:32</span> </span> </div>';
		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertArrayHasKey('start', $output['items'][0]['properties']);
		$this->assertEquals('2014-06-06T18:32', $output['items'][0]['properties']['start'][0]);
	}

	/**
	 * @group parseDT
	 * @group valueClass
	 */
	public function testDTStartDateOnly() {
		$input = '<div class="h-event"> <span class="dt-start"><span class="value">2014-06-07</span> </span> </div>';
		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertArrayHasKey('start', $output['items'][0]['properties']);
		$this->assertEquals('2014-06-07', $output['items'][0]['properties']['start'][0]);
	}

}
