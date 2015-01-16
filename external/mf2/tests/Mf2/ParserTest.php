<?php

namespace Mf2\Parser\Test;

use Mf2\Parser;
use Mf2;
use PHPUnit_Framework_TestCase;

/**
 * Parser Test
 * 
 * Contains tests for internal parsing functions and stuff which doesn’t go anywhere else, i.e.
 * isn’t related to a particular property as such.
 * 
 * Stuff for parsing E goes in here until there is enough of it to go elsewhere (like, never?)
 */
class ParserTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		date_default_timezone_set('Europe/London');
	}
	
	public function testUnicodeTrim() {
		$this->assertEquals('thing', Mf2\unicodeTrim('  thing  '));
		$this->assertEquals('thing', Mf2\unicodeTrim('			thing			'));
		$this->assertEquals('thing', Mf2\unicodeTrim(mb_convert_encoding(' &nbsp; thing &nbsp; ', 'UTF-8', 'HTML-ENTITIES') ));
	}
	
	public function testMicroformatNameFromClassReturnsFullRootName() {
		$expected = array('h-card');
		$actual = Mf2\mfNamesFromClass('someclass h-card someotherclass', 'h-');

		$this->assertEquals($actual, $expected);
	}

	public function testMicroformatNameFromClassHandlesMultipleHNames() {
		$expected = array('h-card', 'h-person');
		$actual = Mf2\mfNamesFromClass('someclass h-card someotherclass h-person yetanotherclass', 'h-');

		$this->assertEquals($actual, $expected);
	}

	public function testMicroformatStripsPrefixFromPropertyClassname() {
		$expected = array('name');
		$actual = Mf2\mfNamesFromClass('someclass p-name someotherclass', 'p-');

		$this->assertEquals($actual, $expected);
	}

	public function testNestedMicroformatPropertyNameWorks() {
		$expected = array('location');
		$test = 'someclass p-location someotherclass';
		$actual = Mf2\nestedMfPropertyNamesFromClass($test);
		
		$this->assertEquals($actual, $expected);
	}
	
	public function testParseE() {
		$input = '<div class="h-entry"><div class="e-content">Here is a load of <strong>embedded markup</strong></div></div>';
		//$parser = new Parser($input);
		$output = Mf2\parse($input);

		$this->assertArrayHasKey('content', $output['items'][0]['properties']);
		$this->assertEquals('Here is a load of <strong>embedded markup</strong>', $output['items'][0]['properties']['content'][0]['html']);
		$this->assertEquals('Here is a load of embedded markup', $output['items'][0]['properties']['content'][0]['value']);
	}
	
	public function testParseEResolvesRelativeLinks() {
		$input = '<div class="h-entry"><p class="e-content">Blah blah <a href="/a-url">thing</a>. <object data="/object"></object> <img src="/img" /></p></div>';
		$parser = new Parser($input, 'http://example.com');
		$output = $parser->parse();
		
		$this->assertEquals('Blah blah <a href="http://example.com/a-url">thing</a>. <object data="http://example.com/object"></object> <img src="http://example.com/img"></img>', $output['items'][0]['properties']['content'][0]['html']);
		$this->assertEquals('Blah blah thing.', $output['items'][0]['properties']['content'][0]['value']);
	}

	/**
	 * @group parseH
	 */
	public function testInvalidClassnamesContainingHAreIgnored() {
		$input = '<div class="asdfgh-jkl"></div>';
		$parser = new Parser($input);
		$output = $parser->parse();

		// Look through $output for an item which indicate failure
		foreach ($output['items'] as $item) {
			if (in_array('asdfgh-jkl', $item['type']))
				$this->fail();
		}
	}
	
	public function testHtmlSpecialCharactersWorks() {
		$this->assertEquals('&lt;&gt;', htmlspecialchars('<>'));
	}
	
	public function testHtmlEncodesNonEProperties() {
		$input = '<div class="h-card">
			<span class="p-name">&lt;p&gt;</span>
			<span class="dt-published">&lt;dt&gt;</span>
			<span class="u-url">&lt;u&gt;</span>
			</div>';
		
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertEquals('<p>', $output['items'][0]['properties']['name'][0]);
		$this->assertEquals('<dt>', $output['items'][0]['properties']['published'][0]);
		$this->assertEquals('<u>', $output['items'][0]['properties']['url'][0]);
	}
	
	public function testHtmlEncodesImpliedProperties() {
		$input = '<a class="h-card" href="&lt;url&gt;"><img src="&lt;img&gt;" />&lt;name&gt;</a>';
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertEquals('<name>', $output['items'][0]['properties']['name'][0]);
		$this->assertEquals('<url>', $output['items'][0]['properties']['url'][0]);
		$this->assertEquals('<img>', $output['items'][0]['properties']['photo'][0]);
	}
	
	public function testParsesRelValues() {
		$input = '<a rel="author" href="http://example.com">Mr. Author</a>';
		$parser = new Parser($input);
		
		$output = $parser->parse();
		
		$this->assertArrayHasKey('rels', $output);
		$this->assertEquals('http://example.com', $output['rels']['author'][0]);
	}
	
	public function testParsesRelAlternateValues() {
		$input = '<a rel="alternate home" href="http://example.org" hreflang="de", media="screen"></a>';
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertArrayHasKey('alternates', $output);
		$this->assertEquals('http://example.org', $output['alternates'][0]['url']);
		$this->assertEquals('home', $output['alternates'][0]['rel']);
		$this->assertEquals('de', $output['alternates'][0]['hreflang']);
		$this->assertEquals('screen', $output['alternates'][0]['media']);
	}
	
	public function testParseFromIdOnlyReturnsMicroformatsWithinThatId() {
		$input = <<<EOT
<div class="h-entry"><span class="p-name">Not Included</span></div>

<div id="parse-here">
	<span class="h-card">Included</span>
</div>

<div class="h-entry"><span class="p-name">Not Included</span></div>
EOT;
		
		$parser = new Parser($input);
		$output = $parser->parseFromId('parse-here');
		
		$this->assertCount(1, $output['items']);
		$this->assertEquals('Included', $output['items'][0]['properties']['name'][0]);
	}
	
	/**
	 * Issue #21 github.com/indieweb/php-mf2/issues/21
	 */
	public function testDoesntAddArraysWithOnlyValueForAlreadyParsedNestedMicroformats() {
		$input = <<<EOT
<div class="h-entry">
	<div class="p-in-reply-to h-entry">
		<span class="p-author h-card">Nested Author</span>
	</div>
	
	<span class="p-author h-card">Real Author</span>
</div>
EOT;
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertCount(1, $output['items'][0]['properties']['author']);
	}
	
	public function testParsesNestedMicroformatsWithClassnamesInAnyOrder() {
		$input = <<<EOT
<div class="h-entry">
	<div class="note- p-in-reply-to h-entry">Name</div>
</div>
EOT;
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertCount(1, $output['items'][0]['properties']['in-reply-to']);
		$this->assertEquals('Name', $output['items'][0]['properties']['in-reply-to'][0]['properties']['name'][0]);
	}
}
