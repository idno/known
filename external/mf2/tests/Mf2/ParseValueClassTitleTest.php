<?php

/**
 * Tests of the parsing methods within mf2\Parser
 */

namespace Mf2\Parser\Test;

use Mf2;
use Mf2\Parser;
use PHPUnit_Framework_TestCase;

class ParseValueClassTitleTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		date_default_timezone_set('Europe/London');
	}
	
	public function testValueClassTitleHandlesSingleValueClass() {
		$input = '<div class="h-card"><p class="p-name"><span class="value">Name</span> (this should not be included)</p></div>';
		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertArrayHasKey('name', $output['items'][0]['properties']);
		$this->assertEquals('Name', $output['items'][0]['properties']['name'][0]);
	}
	
	public function testValueClassTitleHandlesMultipleValueClass() {
		$input = '<div class="h-card"><p class="p-name"><span class="value">Name</span> (this should not be included) <span class="value">Endname</span></p></div>';
		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertArrayHasKey('name', $output['items'][0]['properties']);
		$this->assertEquals('NameEndname', $output['items'][0]['properties']['name'][0]);
	}
	
	public function testValueClassTitleHandlesSingleValueTitle() {
		$input = '<div class="h-card"><p class="p-name"><span class="value-title" title="Real Name">Wrong Name</span> (this should not be included)</p></div>';
		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertArrayHasKey('name', $output['items'][0]['properties']);
		$this->assertEquals('Real Name', $output['items'][0]['properties']['name'][0]);
	}
	
	public function testValueClassTitleHandlesMultipleValueTitle() {
		$input = '<div class="h-card"><p class="p-name"><span class="value-title" title="Real ">Wrong Name</span> <span class="value-title" title="Name">(this should not be included)</span></p></div>';
		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertArrayHasKey('name', $output['items'][0]['properties']);
		$this->assertEquals('Real Name', $output['items'][0]['properties']['name'][0]);
	}
	
	/**
	 * @see https://github.com/indieweb/php-mf2/issues/25
	 */
	public function testValueClassDatetimeWorksWithUrlProperties() {
		$input = <<<EOT
<div class="h-entry">
	<a href="2013/178/t1/surreal-meeting-dpdpdp-trondisc"
		rel="bookmark"
		class="dt-published published dt-updated updated u-url u-uid">
			<time class="value">10:17</time>
			on <time class="value">2013-06-27</time>
	</a>
</div>
EOT;
		
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertArrayHasKey('published', $output['items'][0]['properties']);
		$this->assertEquals('2013-06-27T10:17', $output['items'][0]['properties']['published'][0]);
	}
	
	/**
	 * @see https://github.com/indieweb/php-mf2/issues/27
	 */
	public function testParsesValueTitleDatetimes() {
		$input = <<<EOT
<div class="h-entry">
 <h1 class="p-name">test</h1>
 <span class="dt-published"><span class="value-title" title="2012-02-16T16:14:47+00:00"> </span>16.02.2012</span>
</div>
EOT;
		
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertEquals('2012-02-16T16:14:47+00:00', $output['items'][0]['properties']['published'][0]);
	}
	
	/** @see https://github.com/indieweb/php-mf2/issues/34 */
	public function testIgnoresValueClassNestedFurtherThanChild() {
		$test = '<div class="h-card"><span class="p-tel"><span class="value">1234</span><span class="h-card"><span class="p-tel"><span class="value">5678</span>';
		$result = Mf2\parse($test);
		$this->assertEquals('1234', $result['items'][0]['properties']['tel'][0]);
		$this->assertEquals('5678', $result['items'][0]['children'][0]['properties']['tel'][0]);
	}
	
	/** @see https://github.com/indieweb/php-mf2/issues/38 */
	public function testValueClassDtMatchesSingleDigitTimeComponent() {
		$test = '<div class="h-entry"><span class="dt-published"><time class="value">6:01</time>, <time class="value">2013-02-01</time></span></div>';
		$result = Mf2\parse($test);
		$this->assertEquals('2013-02-01T6:01', $result['items'][0]['properties']['published'][0]);
	}
}
