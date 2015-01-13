<?php

/**
 * Tests of the parsing methods within mf2\Parser
 */

namespace Mf2\Parser\Test;

use Mf2\Parser;
use Mf2;
use PHPUnit_Framework_TestCase;

/**
 * Classic Microformats Test
 * 
 * Contains tests of the classic microformat => µf2 functionality.
 * 
 * Mainly based off BC tables on http://microformats.org/wiki/microformats2#v2_vocabularies
 */
class ClassicMicroformatsTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		date_default_timezone_set('Europe/London');
	}
	
	public function testParsesClassicHcard() {
		$input = '<div class="vcard"><span class="fn n">Barnaby Walters</span> is a person.</div>';
		$expected = '{"items": [{"type": ["h-card"], "properties": {"name": ["Barnaby Walters"]}}], "rels": {}}';
		$parser = new Parser($input);
		$this->assertJsonStringEqualsJsonString(json_encode($parser->parse()), $expected);
	}
	
	public function testParsesClassicHEntry() {
		$input = '<div class="hentry"><h1 class="entry-title">microformats2 Is Great</h1> <p class="entry-summary">yes yes it is.</p></div>';
		$expected = '{"items": [{"type": ["h-entry"], "properties": {"name": ["microformats2 Is Great"], "summary": ["yes yes it is."]}}], "rels": {}}';
		$parser = new Parser($input);
		$this->assertJsonStringEqualsJsonString(json_encode($parser->parse()), $expected);
	}
	
	public function testIgnoresClassicClassnamesUnderMf2Root() {
		$input = <<<EOT
<div class="h-entry">
	<p class="author">Not Me</p>
	<p class="p-author h-card">I wrote this</p>
</div>
EOT;
		$parser = new Parser($input);
		$result = $parser->parse();
		$this->assertEquals('I wrote this', $result['items'][0]['properties']['author'][0]['properties']['name'][0]);
		
	}
	
	public function testIgnoresClassicPropertyClassnamesOutsideClassicRoots() {
		$input = <<<EOT
<p class="author">Mr. Invisible</p>
EOT;
		$parser = new Parser($input);
		$result = $parser->parse();
		$this->assertCount(0, $result['items']);
	}
	
	public function testParsesFBerrimanClassicHEntry() {
		$input = <<<EOT
<article id="post-976" class="post-976 post type-post status-publish format-standard hentry category-speaking category-web-dev tag-conferences tag-front-trends tag-fronttrends tag-speaking tag-txjs">
	<header class="entry-header">
		<h1 class="entry-title">
			<a href="http://fberriman.com/2013/05/14/april-recap-txjs-front-trends/" rel="bookmark">April recap &#8211; TXJS &#038; Front-Trends</a>
		</h1>
		
		<div class="entry-meta">
			<span class="date">
				<a href="http://fberriman.com/2013/05/14/april-recap-txjs-front-trends/" title="Permalink to April recap &#8211; TXJS &amp; Front-Trends" rel="bookmark">
					<time class="entry-date" datetime="2013-05-14T11:54:06+00:00">May 14, 2013</time>
				</a>
			</span>
			<span class="categories-links">
				<a href="http://fberriman.com/category/speaking/" title="View all posts in Speaking" rel="category tag">Speaking</a>,
				<a href="http://fberriman.com/category/web-dev/" title="View all posts in Web Dev" rel="category tag">Web Dev</a>
			</span>
			<span class="tags-links">
				<a href="http://fberriman.com/tag/conferences/" rel="tag">conferences</a>,
				<a href="http://fberriman.com/tag/front-trends/" rel="tag">front-trends</a>,
				<a href="http://fberriman.com/tag/fronttrends/" rel="tag">fronttrends</a>,
				<a href="http://fberriman.com/tag/speaking/" rel="tag">Speaking</a>,
				<a href="http://fberriman.com/tag/txjs/" rel="tag">txjs</a>
			</span>
			<span class="author vcard"><a class="url fn n" href="http://fberriman.com/author/admin/" title="View all posts by Frances" rel="author">Frances</a></span>					</div>
	</header>

		<div class="entry-content">
		<p>April was pretty decent.  I got to attend two very good conferences <strong>and</strong> I got to speak at them.</p>
			</div>
	
	<footer class="entry-meta">
		<div class="comments-link">
			<a href="http://fberriman.com/2013/05/14/april-recap-txjs-front-trends/#respond" title="Comment on April recap &#8211; TXJS &amp; Front-Trends"><span class="leave-reply">Leave a comment</span></a>
		</div>

	</footer><!-- .entry-meta -->
</article><!-- #post -->
EOT;
		$parser = new Parser($input);
		$result = $parser->parse();
		$e = $result['items'][0];
		$this->assertContains('h-entry', $e['type']);
	}
	
	public function testParsesSnarfedOrgArticleCorrectly() {
		$input = file_get_contents(__DIR__ . '/snarfed.org.html');
		/*$parser = new Parser($input, 'http://snarfed.org/2013-10-23_oauth-dropins');
		$result = $parser->parse();/**/
		$result = Mf2\parse($input, 'http://snarfed.org/2013-10-23_oauth-dropins');
		print_r($result);
	}
}
