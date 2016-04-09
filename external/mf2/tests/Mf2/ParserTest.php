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

		$this->assertEquals($expected, $actual);
	}

	public function testMicroformatNameFromClassHandlesMultipleHNames() {
		$expected = array('h-card', 'h-person');
		$actual = Mf2\mfNamesFromClass('someclass h-card someotherclass h-person yetanotherclass', 'h-');

		$this->assertEquals($expected, $actual);
	}

	public function testMicroformatStripsPrefixFromPropertyClassname() {
		$expected = array('name');
		$actual = Mf2\mfNamesFromClass('someclass p-name someotherclass', 'p-');

		$this->assertEquals($expected, $actual);
	}

	public function testNestedMicroformatPropertyNameWorks() {
		$expected = array('location' => array('p-'), 'author' => array('u-', 'p-'));
		$test = 'someclass p-location someotherclass u-author p-author';
		$actual = Mf2\nestedMfPropertyNamesFromClass($test);

		$this->assertEquals($expected, $actual);
	}

	public function testMicroformatNamesFromClassIgnoresPrefixesWithoutNames() {
		$expected = array();
		$actual = Mf2\mfNamesFromClass('someclass h- someotherclass', 'h-');

		$this->assertEquals($expected, $actual);
	}

	public function testMicroformatNamesFromClassHandlesExcessiveWhitespace() {
		$expected = array('h-card');
		$actual = Mf2\mfNamesFromClass('  someclass
			 	h-card 	 someotherclass		   	 ', 'h-');

		$this->assertEquals($expected, $actual);
	}

	public function testMicroformatNamesFromClassIgnoresUppercaseClassnames() {
		$expected = array();
		$actual = Mf2\mfNamesFromClass('H-ENTRY', 'h-');

		$this->assertEquals($expected, $actual);
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
		$this->assertEquals('Blah blah thing.  http://example.com/img', $output['items'][0]['properties']['content'][0]['value']);
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
		$input = '<a rel="alternate home" href="http://example.org" hreflang="de", media="screen" type="text/html" title="German Homepage Link">German Homepage</a>';
		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertArrayHasKey('alternates', $output);
		$this->assertEquals('http://example.org', $output['alternates'][0]['url']);
		$this->assertEquals('home', $output['alternates'][0]['rel']);
		$this->assertEquals('de', $output['alternates'][0]['hreflang']);
		$this->assertEquals('screen', $output['alternates'][0]['media']);
		$this->assertEquals('text/html', $output['alternates'][0]['type']);
		$this->assertEquals('German Homepage Link', $output['alternates'][0]['title']);
		$this->assertEquals('German Homepage', $output['alternates'][0]['text']);
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

	/**
	 * @group network
	 */
	public function testFetchMicroformats() {
		$mf = Mf2\fetch('http://waterpigs.co.uk/');
		$this->assertArrayHasKey('items', $mf);

		$mf = Mf2\fetch('http://waterpigs.co.uk/photo.jpg', null, $curlInfo);
		$this->assertNull($mf);
		$this->assertContains('jpeg', $curlInfo['content_type']);
	}

	/**
	 * @see https://github.com/indieweb/php-mf2/issues/48
	 */
	public function testIgnoreClassesEndingInHyphen() {
		$input = '<span class="h-entry"> <span class="e-">foo</span> </span>';
		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertArrayNotHasKey('0', $output['items'][0]['properties']);
	}

	/**
	 * @see https://github.com/indieweb/php-mf2/issues/52
	 * @see https://github.com/tommorris/mf2py/commit/92740deb7e19b8f1e7fbf6bec001cf52f2b07e99
	 */
	public function testIgnoresTemplateElements() {
		$result = Mf2\parse('<template class="h-card"><span class="p-name">Tom Morris</span></template>');
		$this->assertCount(0, $result['items']);
	}

	/**
	 * @see https://github.com/indieweb/php-mf2/issues/53
	 * @see http://microformats.org/wiki/microformats2-parsing#parsing_an_e-_property
	 */
	public function testConvertsNestedImgElementToAltOrSrc() {
		$input = <<<EOT
<div class="h-entry">
	<p class="e-content">It is a strange thing to see a <img alt="five legged elephant" src="/photos/five-legged-elephant.jpg" /></p>
</div>
EOT;
		$result = Mf2\parse($input, 'http://waterpigs.co.uk/articles/five-legged-elephant');
		$this->assertEquals('It is a strange thing to see a five legged elephant', $result['items'][0]['properties']['content'][0]['value']);
	}

	// parser not respecting not[h-*] in rule  "else if .h-x>a[href]:only-of-type:not[.h-*] then use that [href] for url"
	public function testNotImpliedUrlFromHCard() {
		$input = <<<EOT
<span class="h-entry">
	<a class="h-card" href="http://test.com">John Q</a>
</span>
EOT;

		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertArrayNotHasKey('url', $output['items'][0]['properties']);
	}

	public function testAreaTag() {
		$input = <<<EOT
<div class="h-entry">
	<area class="p-category h-card" href="http://personB.example.com" alt="Person Bee" shape="rect" coords="100,100,120,120">
</div>
EOT;

		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertEquals('Person Bee', $output['items'][0]['properties']['name'][0]);
		$this->assertEquals('rect', $output['items'][0]['properties']['category'][0]['shape']);
		$this->assertEquals('100,100,120,120', $output['items'][0]['properties']['category'][0]['coords']);
		$this->assertEquals('Person Bee', $output['items'][0]['properties']['category'][0]['value']);
	}

	public function testParseHcardInCategory() {
		$input = <<<EOT
<span class="h-entry">
	<a class="p-author h-card" href="http://a.example.com/">Alice</a> tagged
	<a href="http://b.example.com/" class="u-category h-card">Bob Smith</a> in
	<a class="u-tag-of u-in-reply-to" href="http://s.example.com/permalink47">
		<img src="http://s.example.com/photo47.png" alt="a photo of Bob and Cole" />
	</a>
</span>
EOT;

		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertContains('h-entry', $output['items'][0]['type']);
		$this->assertArrayHasKey('category', $output['items'][0]['properties']);
		$this->assertContains('h-card', $output['items'][0]['properties']['category'][0]['type']);
		$this->assertArrayHasKey('name', $output['items'][0]['properties']['category'][0]['properties']);
		$this->assertEquals('Bob Smith', $output['items'][0]['properties']['category'][0]['properties']['name'][0]);
		$this->assertArrayHasKey('url', $output['items'][0]['properties']['category'][0]['properties']);
		$this->assertEquals('http://b.example.com/', $output['items'][0]['properties']['category'][0]['properties']['url'][0]);
	}
	
	public function testApplyTransformationToSrcset() {
		$transformation = function ($url) {
			return 'https://example.com/' . ltrim($url, '/');
		};
		
		// Example from https://developers.whatwg.org/edits.html#attr-img-srcset
		$srcset = 'banner-HD.jpeg 2x, banner-phone.jpeg 100w, banner-phone-HD.jpeg 100w 2x';
		$result = Mf2\applySrcsetUrlTransformation($srcset, $transformation);
		$this->assertEquals('https://example.com/banner-HD.jpeg 2x, https://example.com/banner-phone.jpeg 100w, https://example.com/banner-phone-HD.jpeg 100w 2x', $result);
	}


	/**
	 * @see https://github.com/indieweb/php-mf2/issues/84
	 */
	public function testRelativeURLResolvedWithFinalURL() {
		$mf = Mf2\fetch('http://aaron.pk/4Zn5');

		$this->assertEquals('https://aaronparecki.com/2014/12/23/5/photo.jpeg', $mf['items'][0]['properties']['photo'][0]);
	}
	
	public function testScriptTagContentsRemovedFromTextValue() {
		$input = <<<EOT
<div class="h-entry">
	<div class="p-content">
		<b>Hello World</b>
		<script>alert("hi");</script>
	</div>
</div>
EOT;

		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertContains('h-entry', $output['items'][0]['type']);
		$this->assertContains('Hello World', $output['items'][0]['properties']['content'][0]);
		$this->assertNotContains('alert', $output['items'][0]['properties']['content'][0]);
	}
	
	public function testScriptElementContentsRemovedFromAllPlaintextValues() {
		$input = <<<EOT
<div class="h-entry">
	<span class="dt-published">contained<script>not contained</script><style>not contained</style></span>
	<span class="u-url">contained<script>not contained</script><style>not contained</style></span>
</div>
EOT;

		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertNotContains('not contained', $output['items'][0]['properties']['published'][0]);
		$this->assertNotContains('not contained', $output['items'][0]['properties']['url'][0]);
	}

	public function testScriptTagContentsNotRemovedFromHTMLValue() {
		$input = <<<EOT
<div class="h-entry">
	<div class="e-content">
		<b>Hello World</b>
		<script>alert("hi");</script>
		<style>body{ visibility: hidden; }</style>
		<p>
			<script>alert("hi");</script>
			<style>body{ visibility: hidden; }</style>
		</p>
	</div>
</div>
EOT;

		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertContains('h-entry', $output['items'][0]['type']);
		$this->assertContains('Hello World', $output['items'][0]['properties']['content'][0]['value']);
		$this->assertContains('<b>Hello World</b>', $output['items'][0]['properties']['content'][0]['html']);
		# The script and style tags should be removed from plaintext results but left in HTML results.
		$this->assertContains('alert', $output['items'][0]['properties']['content'][0]['html']);
		$this->assertNotContains('alert', $output['items'][0]['properties']['content'][0]['value']);
		$this->assertContains('visibility', $output['items'][0]['properties']['content'][0]['html']);
		$this->assertNotContains('visibility', $output['items'][0]['properties']['content'][0]['value']);
	}
	
	public function testWhitespaceBetweenElements() {
		$input = <<<EOT
<div class="h-entry">
	<data class="p-rsvp" value="yes">I'm attending</data>
	<a class="u-in-reply-to" href="https://snarfed.org/2014-06-16_homebrew-website-club-at-quip">Homebrew Website Club at Quip</a>
	<div class="p-content">Thanks for hosting!</div>
</div>
EOT;

		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertContains('h-entry', $output['items'][0]['type']);
		$this->assertNotContains('attendingHomebrew', $output['items'][0]['properties']['name'][0]);
	}
}
