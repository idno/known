<?php

namespace Mf2\Parser\Test;

use Mf2\Parser;
use Mf2;
use PHPUnit_Framework_TestCase;

/**
 * Combined Microformats Test
 * 
 * Tests the ability of Parser::parse() to handle nested microformats correctly.
 * More info at http://microformats.org/wiki/microformats-2#combining_microformats
 * 
 * @todo implement
 */
class CombinedMicroformatsTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		date_default_timezone_set('Europe/London');
	}

	/**
	 * From http://microformats.org/wiki/microformats2#combining_microformats
	 */
	public function testHEventLocationHCard() {
		$input = '<div class="h-event">
	<a class="p-name u-url" href="http://indiewebcamp.com/2012">
	IndieWebCamp 2012
	</a>
	from <time class="dt-start">2012-06-30</time> 
	to <time class="dt-end">2012-07-01</time> at 
	<span class="p-location h-card">
	<a class="p-name p-org u-url" href="http://geoloqi.com/">
	Geoloqi</a>, <span class="p-street-address">920 SW 3rd Ave. Suite 400</span>, <span class="p-locality">Portland</span>, <abbr class="p-region" title="Oregon">OR</abbr>
  </span>
</div>';
		$expected = '{
	"rels": {},
	"items": [{ 
		"type": ["h-event"],
		"properties": {
			"name": ["IndieWebCamp 2012"],
			"url": ["http://indiewebcamp.com/2012"],
			"start": ["2012-06-30"],
			"end": ["2012-07-01"],
			"location": [{
				"value": "Geoloqi",
				"type": ["h-card"],
				"properties": {
					"name": ["Geoloqi"],
					"org": ["Geoloqi"],
					"url": ["http://geoloqi.com/"],
					"street-address": ["920 SW 3rd Ave. Suite 400"],
					"locality": ["Portland"],
					"region": ["Oregon"]
				}
			}]
		}
	}]
}';

		$parser = new Parser($input, '', true);
		$parser->stringDateTimes = true;
		$output = $parser->parse();
		
		$this->assertJsonStringEqualsJsonString(json_encode($output), $expected);
	}

	/**
	 * From http://microformats.org/wiki/microformats2#combining_microformats
	 */
	public function testHCardOrgPOrg() {
		$input = '<div class="h-card">
  <a class="p-name u-url"
	 href="http://blog.lizardwrangler.com/" 
	>Mitchell Baker</a> 
  (<span class="p-org">Mozilla Foundation</span>)
</div>';
		$expected = '{
	"rels": {},
  "items": [{ 
	"type": ["h-card"],
	"properties": {
	  "name": ["Mitchell Baker"],
	  "url": ["http://blog.lizardwrangler.com/"],
	  "org": ["Mozilla Foundation"]
	}
  }]
}';

		$parser = new Parser($input, '', true);
		$parser->stringDateTimes = true;
		$output = $parser->parse();

		$this->assertJsonStringEqualsJsonString(json_encode($output), $expected);
	}

	/**
	 * From http://microformats.org/wiki/microformats2#combining_microformats
	 */
	public function testHCardOrgHCard() {
		$input = '<div class="h-card">
  <a class="p-name u-url"
	 href="http://blog.lizardwrangler.com/" 
	>Mitchell Baker</a> 
  (<a class="p-org h-card" 
	  href="http://mozilla.org/"
	 >Mozilla Foundation</a>)
</div>';
		$expected = '{
	"rels": {},
  "items": [{ 
	"type": ["h-card"],
	"properties": {
	  "name": ["Mitchell Baker"],
	  "url": ["http://blog.lizardwrangler.com/"],
	  "org": [{
		"value": "Mozilla Foundation",
		"type": ["h-card"],
		"properties": {
		  "name": ["Mozilla Foundation"],
		  "url": ["http://mozilla.org/"]
		 }
	  }]
	}
  }]
}';

		$parser = new Parser($input, '', true);
		$parser->stringDateTimes = true;
		$output = $parser->parse();

		$this->assertJsonStringEqualsJsonString(json_encode($output), $expected);
	}

	/**
	 * From http://microformats.org/wiki/microformats2#combining_microformats
	 */
	public function testHCardPOrgHCardHOrg() {
		$input = '<div class="h-card">
  <a class="p-name u-url"
	 href="http://blog.lizardwrangler.com/" 
	>Mitchell Baker</a> 
  (<a class="p-org h-card h-org" 
	  href="http://mozilla.org/"
	 >Mozilla Foundation</a>)
</div>';
		$expected = '{
	"rels": {},
  "items": [{ 
	"type": ["h-card"],
	"properties": {
	  "name": ["Mitchell Baker"],
	  "url": ["http://blog.lizardwrangler.com/"],
	  "org": [{
		"value": "Mozilla Foundation",
		"type": ["h-card", "h-org"],
		"properties": {
		  "name": ["Mozilla Foundation"],
		  "url": ["http://mozilla.org/"]
		}
	  }]
	}
  }]
}';

		$parser = new Parser($input, '', true);
		$output = $parser->parse();
		
		$this->assertJsonStringEqualsJsonString(json_encode($output), $expected);
	}

	/**
	 * From http://microformats.org/wiki/microformats2#combining_microformats
	 */
	public function testHCardChildHCard() {
		$input = '<div class="h-card">
	<a class="p-name u-url"
	href="http://blog.lizardwrangler.com/">
	Mitchell Baker</a> 
	(<a class="h-card h-org" href="http://mozilla.org/">
	Mozilla Foundation</a>)
</div>';
		$expected = '{
	"rels": {},
  "items": [{ 
		"type": ["h-card"],
		"properties": {
			"name": ["Mitchell Baker"],
			"url": ["http://blog.lizardwrangler.com/"]
		},
		"children": [{
			"type": ["h-card","h-org"],
			"properties": {
				"name": ["Mozilla Foundation"],
				"url": ["http://mozilla.org/"]
			},
			"value": "Mozilla Foundation"
		}]
	}]
}';

		$parser = new Parser($input, '', true);
		$output = $parser->parse();
		
		$this->assertJsonStringEqualsJsonString(json_encode($output), $expected);
	}
	
	/**
	 * Regression test for https://github.com/indieweb/php-mf2/issues/42
	 * 
	 * This was occurring because mfPropertyNamesFromClass was only ever returning the first property name
	 * rather than all of them.
	 */
	public function testNestedMicroformatUnderMultipleProperties() {
		$input = '<article class="h-entry"><div class="p-like-of p-in-reply-to h-cite"></div></article>';
		$mf = Mf2\parse($input);
		
		$this->assertCount(1, $mf['items'][0]['properties']['like-of']);
		$this->assertCount(1, $mf['items'][0]['properties']['in-reply-to']);
	}
	
	/**
	 * Test microformats nested under e-* property classnames retain html: key in structure
	 * 
	 * @see https://github.com/indieweb/php-mf2/issues/64
	 */
	public function testMicroformatsNestedUnderEPropertyClassnamesRetainHtmlKey() {
		$input = '<div class="h-entry"><div class="h-card e-content"><p>Hello</p></div></div>';
		$mf = Mf2\parse($input);
		
		$this->assertEquals($mf['items'][0]['properties']['content'][0]['html'], '<p>Hello</p>');
	}
	
	/**
	 * Test microformats nested under u-* property classnames derive value: key from parsing as u-*
	 */
	public function testMicroformatsNestedUnderUPropertyClassnamesDeriveValueCorrectly() {
		$input = '<div class="h-entry"><img class="u-url h-card" alt="This should not be the value" src="This should be the value" /></div>';
		$mf = Mf2\parse($input);
		
		$this->assertEquals($mf['items'][0]['properties']['url'][0]['value'], 'This should be the value');
	}

	public function testMicroformatsNestedUnderUPropertyClassnamesDeriveValueFromURL() {
		$input = '<div class="h-entry">
		  <h1 class="p-name">Name</h1>
		  <p class="e-content">Hello World</p>
		  <ul>
		    <li class="u-comment h-cite">
		    	<a class="u-author h-card" href="http://jane.example.com/">Jane Bloggs</a>
		    	<p class="p-content p-name">lol</p>
		    	<a class="u-url" href="http://example.org/post1234"><time class="dt-published">2015-07-12 12:03</time></a>
		    </li>
		  </ul>
		</div>';
		$expected = '{
		  "items": [{
    	  "type": ["h-entry"],
	      "properties": {
	        "name": ["Name"],
	        "content": [{
	          "html": "Hello World",
	          "value": "Hello World"
	        }],
	        "comment": [{
            "type": ["h-cite"],
            "properties": {
              "author": [{
                "type": ["h-card"],
                "properties": {
                  "name": ["Jane Bloggs"],
                  "url": ["http:\/\/jane.example.com\/"]
                },
                "value": "http:\/\/jane.example.com\/"
              }],
              "content": ["lol"],
              "name": ["lol"],
              "url": ["http:\/\/example.org\/post1234"],
              "published": ["2015-07-12 12:03"]
            },
            "value": "http:\/\/example.org\/post1234"
          }]
	      }
	    }],
	    "rels":[]
	  }';
	  	$mf = Mf2\parse($input);

		$this->assertJsonStringEqualsJsonString(json_encode($mf), $expected);
		$this->assertEquals($mf['items'][0]['properties']['comment'][0]['value'], 'http://example.org/post1234');
		$this->assertEquals($mf['items'][0]['properties']['comment'][0]['properties']['author'][0]['value'], 'http://jane.example.com/');
	}
	
	public function testMicroformatsNestedUnderPPropertyClassnamesDeriveValueFromFirstPName() {
		$input = '<div class="h-entry"><div class="p-author h-card">This post was written by <span class="p-name">Zoe</span>.</div></div>';
		$mf = Mf2\parse($input);
		
		$this->assertEquals($mf['items'][0]['properties']['author'][0]['value'], 'Zoe');
	}
}
