<?php

/**
 * Tests of the parsing methods within mf2\Parser
 */

namespace Mf2\Parser\Test;

use Mf2\Parser;
use PHPUnit_Framework_TestCase;

/**
 * Microformats Wiki Examples
 * 
 * Contains tests built directly from examples given on the microformats wiki pages about µf2.
 * 
 * These tend to compare the JSON-encoded output of Parser::parse() with the JSON strings given on the wiki.
 * If the given JSON is not within an `items` key of the root object, I add it and mark as so.
 * 
 * @author Barnaby Walters waterpigs.co.uk <barnaby@waterpigs.co.uk>
 */
class MicroformatsWikiExamplesTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		date_default_timezone_set('Europe/London');
	}
		
	public function testHandlesEmptyStringsCorrectly() {
		$input = '';
		$expected = '{
	"rels": {},
	"items": []
}';
		
		$parser = new Parser($input, '', true);
		$output = $parser->parse();
		
		$this->assertJsonStringEqualsJsonString(json_encode($output), $expected);
	}
	
	public function testHandlesNullCorrectly() {
		$input = Null;
		$expected = '{
	"rels": {},
	"items": []
}';
		
		$parser = new Parser($input, '', true);
		$parser->jsonMode = true;
		$output = $parser->parse();
		
		$this->assertJsonStringEqualsJsonString(json_encode($output), $expected);
	}
		
	/**
	 * From http://microformats.org/wiki/microformats-2
	 */
	public function testSimplePersonReference() {
		$input = '<span class="h-card">Frances Berriman</span>';
		$expected = '{
	"rels": {},
  "items": [{ 
	"type": ["h-card"],
	"properties": {
	  "name": ["Frances Berriman"] 
	}
  }]
}';
		$parser = new Parser($input, '', true);
		$output = $parser->parse();

		$this->assertJsonStringEqualsJsonString(json_encode($output), $expected);
	}

	/**
	 * From http://microformats.org/wiki/microformats-2
	 */
	public function testSimpleHyperlinkedPersonReference() {
		$input = '<a class="h-card" href="http://benward.me">Ben Ward</a>';
		$expected = '{
	"rels": {},
  "items": [{ 
	"type": ["h-card"],
	"properties": {
	  "name": ["Ben Ward"],
	  "url": ["http://benward.me"]
	}
  }]
}';
		$parser = new Parser($input, '', true);
		$output = $parser->parse();

		$this->assertJsonStringEqualsJsonString(json_encode($output), $expected);
	}

	/**
	 * From http://microformats.org/wiki/microformats-2-implied-properties
	 */
	public function testSimplePersonImage() {
		$input = '<img class="h-card" src="http://example.org/pic.jpg" alt="Chris Messina" />';
		// Added root items key
		$expected = '{
	"rels": {},
	"items": [{ 
  "type": ["h-card"],
  "properties": {
	"name": ["Chris Messina"],
	"photo": ["http://example.org/pic.jpg"]
  }
}]}';
		$parser = new Parser($input, '', true);
		$output = $parser->parse();

		$this->assertJsonStringEqualsJsonString(json_encode($output), $expected);
	}

	/**
	 * From http://microformats.org/wiki/microformats-2-implied-properties
	 */
	public function testHyperlinkedImageNameAndPhotoProperties() {
		$input = '<a class="h-card" href="http://rohit.khare.org/">
 <img alt="Rohit Khare"
	  src="https://s3.amazonaws.com/twitter_production/profile_images/53307499/180px-Rohit-sq_bigger.jpg" />
</a>';
		// Added root items key
		$expected = '{
	"rels": {},
	"items": [{ 
  "type": ["h-card"],
  "properties": {
	"name": ["Rohit Khare"],
	"url": ["http://rohit.khare.org/"],
	"photo": ["https://s3.amazonaws.com/twitter_production/profile_images/53307499/180px-Rohit-sq_bigger.jpg"]
  }
}]}';
		$parser = new Parser($input, '', true);
		$output = $parser->parse();

		$this->assertJsonStringEqualsJsonString(json_encode($output), $expected);
	}

	/**
	 * From http://microformats.org/wiki/microformats-2
	 */
	public function testMoreDetailedPerson() {
		$input = '<div class="h-card">
  <img class="u-photo" alt="photo of Mitchell"
	   src="https://webfwd.org/content/about-experts/300.mitchellbaker/mentor_mbaker.jpg"/>
  <a class="p-name u-url"
	 href="http://blog.lizardwrangler.com/" 
	>Mitchell Baker</a>
 (<a class="u-url" 
	 href="https://twitter.com/MitchellBaker"
	>@MitchellBaker</a>)
  <span class="p-org">Mozilla Foundation</span>
  <p class="p-note">
	Mitchell is responsible for setting the direction and scope of the Mozilla Foundation and its activities.
  </p>
  <span class="p-category">Strategy</span>
  <span class="p-category">Leadership</span>
</div>';

		$expected = '{
  "rels": {},
	"items": [{ 
	"type": ["h-card"],
	"properties": {
	  "photo": ["https://webfwd.org/content/about-experts/300.mitchellbaker/mentor_mbaker.jpg"],
	  "name": ["Mitchell Baker"],
	  "url": [
		"http://blog.lizardwrangler.com/",
		"https://twitter.com/MitchellBaker"
	  ],
	  "org": ["Mozilla Foundation"],
	  "note": ["Mitchell is responsible for setting the direction and scope of the Mozilla Foundation and its activities."],
	  "category": [
		"Strategy",
		"Leadership"
	  ]
	}
  }]
}';
		$parser = new Parser($input, '', true);
		$output = $parser->parse();

		$this->assertJsonStringEqualsJsonString(json_encode($output), $expected);
	}
}
