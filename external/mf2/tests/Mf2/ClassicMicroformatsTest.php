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
		$parser = new Parser($input, '', true);
		$this->assertJsonStringEqualsJsonString(json_encode($parser->parse()), $expected);
	}
	
	public function testParsesClassicHEntry() {
		$input = '<div class="hentry"><h1 class="entry-title">microformats2 Is Great</h1> <p class="entry-summary">yes yes it is.</p></div>';
		$expected = '{"items": [{"type": ["h-entry"], "properties": {"name": ["microformats2 Is Great"], "summary": ["yes yes it is."]}}], "rels": {}}';
		$parser = new Parser($input, '', true);
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
		$result = Mf2\parse($input, 'http://snarfed.org/2013-10-23_oauth-dropins');
	}

	public function testParsesHProduct() {
		$input = <<<'EOT'
<xml id="skufilterbrowse" class="slide">
<productcatalog><labels><label key="skuset.deliverypolicyurl">Delivery policy content URL</label><label key="price.save">Save</label><label key="skuset.seemoredetails">See more details</label><label key="price.additionaloffers">Additional Offers</label><label key="price.freeitem">Includes Free Item*</label><label key="price.instsaving">Instant Savings</label><label key="skuset.eddieseedetails">See details </label><label key="price.rebateurl">RebateURL</label><label key="skuset.freedelivery">FREE SHIPPING, plus 5% back for Rewards Members</label><label key="price.printableCoupons">Click here for Printable Coupon</label><label key="price.value">Value</label><label key="skuset.eddieshipdetails">Estimated to arrive no later than </label><label key="price.qty">Qty.</label><label key="price.chooseyouritems">Choose your Items</label><label key="price.true">true</label><label key="skuset.clearancemessage">&lt;strong&gt;CLEARANCE ITEM:&lt;/strong&gt; </label><label key="price.ERF">Eco Fee</label><label key="skuset.viewlargerimage">View larger</label><label key="common.next">Next</label><label key="price.addtocart">Add to Cart</label><label key="common.reviews">reviews</label><label key="common.previous">Previous</label><label key="skuset.instockonline">In Stock Online</label><label key="price.priceafterrebate">Price &lt;strong&gt;after&lt;/strong&gt; rebate</label><label key="btn.bopis">PICK UP TODAY</label><label key="common.share">SHARE</label><label key="skuset.freedeliverytostore">FREE Shipping to store</label><label key="erf.popup.label">Environmental fee notice:</label><label key="price.details">Details</label><label key="common.stars">stars</label><label key="skuset.learnmore1">Learn more.</label><label key="common.share.twitter">Share on Twitter</label><label key="price.instorespecial">Available In Store Only</label><label key="price.priceincart">See Price in Cart</label><label key="skuset.giftcards">Orders containing this item are not eligible for Gift cards or certain other methods of payment.</label><label key="skuset.mysoftwaredownloads">"My Software Downloads"</label><label key="price.bmsmerf">Buy More Save More prices do not include eco fee.</label><label key="price.now">Now</label><label key="price.collapse">Collapse</label><label key="classpage.getstarted">Get Started</label><label key="skuset.printpage">Print this page</label><label key="price.learnmore">Learn More</label><label key="common.item">Item</label><label key="common.icon.path">/sbdpas/img/ico/</label><label key="skuset.selectcomponent">Select another component below</label><label key="skuset.freeshippingentireorder">Item qualifies entire order for free delivery</label><label key="skuset.eddieincart">in cart. </label><label key="common.selected">selected</label><label key="price.priceaftersavings">Price &lt;strong&gt;after&lt;/strong&gt; savings</label><label key="skuset.inktoner">Ink and toner</label><label key="classpage.comingsoon">Coming Soon</label><label key="price.before">Before</label><label key="price.was">Was</label><label key="skuset.viewfulldetails">View Full Details</label><label key="skuset.expdelivery">Expected Delivery:</label><label key="common.share.pinterest">Share on Pinterest</label><label key="skuset.deliveryonly">Online Only</label><label key="common.share.email">Email it</label><label key="price.seedetails">See Details</label><label key="skuset.expdelpopup">/sbd/content/help-center/shipping-and-delivery.html</label><label key="skuset.softwaredownload">Software Download</label><label key="erf.popup.url">/sbd/content/help/environmental_fee_popup.html</label><label key="price.finalprice">Final Price</label><label key="common.share.facebook">Share on Facebook</label><label key="skuset.eddiesaveproduct">on this product! </label><label key="skuset.suppliedandshippedby">Supplied and Shipped by</label><label key="common.addtofavorites">Add to Favorites</label><label key="skuset.esdnotepart1">Note: Shortly after purchase you will be able to access your Software Downloads in the </label><label key="skuset.esdnotepart2">section of your staples.com&#174; account. It's easy and secure!</label><label key="skuset.software1">/sbd/cre/marketing/technology-research-centers/software/software-downloads.html#z_faq</label><label key="price.instantcoupon">Instant Coupon</label><label key="skuset.eddieshipdetails1">to </label><label key="price.pricebefore">Price &lt;strong&gt;before&lt;/strong&gt;</label><label key="skuset.eddieoffer">Offer valid for 20 minutes. </label><label key="price.price">Price</label><label key="common.model">Model</label><label key="skuset.supplierhover">We have partnered with this trusted supplier to offer you a wider assortment of products and brands for all of your business needs, with the same great level of service you can expect from Staples.com.</label><label key="skuset.forceshiptostore">Item can be shipped only to a retail store location. </label><label key="price.rebate">Rebate</label><label key="price.bmsm">Buy More Save More</label><label key="common.print">print</label><label key="skuset.viewvideo">View video</label><label key="skuset.instoreavailability">Check in Store Availability</label><label key="price.aslowas">As low as</label><label key="price.reg">Reg</label><label key="price.free">FREE</label><label key="erf.message">Provincial recycling or deposit fees may be applicable upon checkout.</label><label key="skuset.eddiesave">Save an extra </label><label key="skuset.deferredfinancemessage">Special Financing Available </label><label key="price.promotions">Promotions</label><label key="price.availableinstore">Available In-Store Only</label><label key="skuset.outofstock">Currently Out of Stock.</label><label key="price.totalsavings">Total Savings</label><label key="price.beforePresentation">Before continuing, please select an item</label></labels>

<product bss="ON" envfeeflag="0" comingsoonflag="0" price="18.35" name="Swingline® 747® Classic Desktop Staplers" bopispilot="true" mss="ON" zipcode="01701" comp="0" presnvalue="Select an Item" snum="SS264184" leadtimedescription="1 - 30 Business Days" expandedpromo="0" alttext="Swingline® 747® Classic Desktop Staplers" prdtypeid="0" presntype="D" review="4.5" class="hproduct" type="skuset" skubopswitch="ON" id="609548" bopisenableflag="0"><descs><desc typeid="2">All-steel construction with non-skid rubber base</desc><desc typeid="2">Spring-loaded inner channel prevents jams</desc><desc typeid="2">Available in black, burgundy and beige &lt;br /&gt; &lt;li&gt;Staples up to 20 sheets&lt;/li&gt;</desc><desc typeid="38">Select an Item</desc><desc typeid="39">All-steel construction with non-skid rubber base</desc><desc typeid="39">Spring-loaded inner channel prevents jams</desc><desc typeid="39">Available in black, burgundy and beige &lt;br /&gt; &lt;li&gt;Staples up to 20 sheets&lt;/li&gt;</desc><desc class="fn">Swingline® 747® Classic Desktop Staplers</desc></descs><price is="18.35" uom="Each"></price><span class="price">18.35</span><span class="currency">USD</span><imgs><pic class="photo" id="1">http://www.staples-3p.com/s7/is/image/Staples/s0021414_sc7?$std$</pic><pic altimg="Y" id="2">http://www.staples-3p.com/s7/is/image/Staples/s0021414_sc7?$thb$</pic><pic id="6">http://www.staples-3p.com/s7/is/image/Staples/s0021414</pic></imgs><producturl class="url">/Swingline-747-Classic-Desktop-Staplers/product_SS264184</producturl><review rating="4.5" count="99" css="45"></review><delivery shiptostore="true" free="false" instore="2" forceshiptostore="false"></delivery></product>

<product bss="ON" envfeeflag="0" comingsoonflag="0" price="18.35" name="Swingline® 747® Classic Desktop Full Strip Stapler, 20 Sheet Capacity, Black" bopispilot="true" mss="ON" zipcode="01701" comp="1" snum="264184" leadtimedescription="1 Business Day" expandedpromo="0" alttext="Swingline® 747® Classic Desktop Full Strip Stapler, 20 Sheet Capacity, Black" prdtypeid="0" review="4.5" class="hproduct" mnum="S7074771G" type="sku" skubopswitch="ON" id="609315" bopisenableflag="1"><descs><desc typeid="2">All-steel construction with non-skid rubber base</desc><desc typeid="2">Full strip</desc><desc typeid="2">Staples up to 20 sheets</desc><desc typeid="19">Each</desc><desc typeid="39">All-steel construction with non-skid rubber base</desc><desc typeid="39">Full strip</desc><desc typeid="39">Staples up to 20 sheets</desc><desc class="fn">Swingline® 747® Classic Desktop Full Strip Stapler, 20 Sheet Capacity, Black</desc></descs><price is="18.35" uom="Each"></price><span class="price">18.35</span><span class="currency">USD</span><imgs><pic class="photo" id="1">http://www.staples-3p.com/s7/is/image/Staples/s0021412_sc7?$std$</pic><pic altimg="Y" id="2">http://www.staples-3p.com/s7/is/image/Staples/s0021412_sc7?$thb$</pic><pic id="6">http://www.staples-3p.com/s7/is/image/Staples/s0021412</pic><pic id="120"></pic></imgs><producturl class="url">/Swingline-747-Classic-Desktop-Full-Strip-Stapler-20-Sheet-Capacity-Black/product_264184</producturl><review rating="4.5" count="99" css="45"></review><delivery shiptostore="true" free="false" instore="1" forceshiptostore="false"></delivery></product>

<product bss="ON" envfeeflag="0" comingsoonflag="0" price="19.59" name="Swingline® 747® Classic Desktop Stapler, Burgundy" bopispilot="true" mss="ON" zipcode="01701" comp="1" snum="413732" leadtimedescription="1 Business Day" expandedpromo="0" alttext="Swingline® 747® Classic Desktop Stapler, Burgundy" prdtypeid="0" review="3.5" class="hproduct" mnum="74718/74782" type="sku" skubopswitch="ON" id="1460639" bopisenableflag="0"><descs><desc typeid="2">All-steel construction with non-skid rubber base</desc><desc typeid="2">Spring-loaded inner channel prevents jams</desc><desc typeid="2">Burgundy &lt;br /&gt; &lt;li&gt;Staples up to 20 sheets&lt;/li&gt;</desc><desc typeid="19">Each</desc><desc typeid="39">All-steel construction with non-skid rubber base</desc><desc typeid="39">Spring-loaded inner channel prevents jams</desc><desc typeid="39">Burgundy &lt;br /&gt; &lt;li&gt;Staples up to 20 sheets&lt;/li&gt;</desc><desc class="fn">Swingline® 747® Classic Desktop Stapler, Burgundy</desc></descs><price is="19.59" uom="Each"></price><span class="price">19.59</span><span class="currency">USD</span><imgs><pic class="photo" id="1">http://www.staples-3p.com/s7/is/image/Staples/m000240695_sc7?$std$</pic><pic altimg="Y" id="2">http://www.staples-3p.com/s7/is/image/Staples/m000240695_sc7?$thb$</pic><pic id="6">http://www.staples-3p.com/s7/is/image/Staples/m000240695</pic></imgs><producturl class="url">/Swingline-747-Classic-Desktop-Stapler-Burgundy/product_413732</producturl><review rating="3.5" count="7" css="35"></review><delivery shiptostore="true" free="false" instore="2" forceshiptostore="false"></delivery></product>

<product bss="ON" envfeeflag="0" comingsoonflag="0" price="39.49" name="Swingline® 747® Rio Red Stapler, 20 Sheet Capacity" bopispilot="true" mss="ON" zipcode="01701" comp="1" brandname="Swingline" snum="562485" leadtimedescription="1 - 4 Business Days" expandedpromo="0" alttext="Swingline® 747® Rio Red Stapler, 20 Sheet Capacity" prdtypeid="0" review="4.5" class="hproduct" mnum="74736" type="sku" skubopswitch="ON" id="1093798" bopisenableflag="0"><descs><desc typeid="2">20 sheet capacity with Swingline S.F.® 4® Staples</desc><desc typeid="2">Durable metal construction</desc><desc typeid="2">Stapler opens for tacking ability</desc><desc typeid="19">Each</desc><desc typeid="39">20 sheet capacity with Swingline S.F.® 4® Staples</desc><desc typeid="39">Durable metal construction</desc><desc typeid="39">Stapler opens for tacking ability</desc><desc class="fn">Swingline® 747® Rio Red Stapler, 20 Sheet Capacity</desc></descs><price is="39.49" uom="Each"></price><span class="price">39.49</span><span class="currency">USD</span><imgs><pic class="photo" id="1">http://www.staples-3p.com/s7/is/image/Staples/s0446269_sc7?$std$</pic><pic altimg="Y" id="2">http://www.staples-3p.com/s7/is/image/Staples/s0446269_sc7?$thb$</pic><pic id="6">http://www.staples-3p.com/s7/is/image/Staples/s0446269</pic></imgs><producturl class="url">/Swingline-747-Rio-Red-Stapler-20-Sheet-Capacity/product_562485</producturl><review rating="4.5" count="76" css="45"></review><delivery shiptostore="true" free="true" instore="2" forceshiptostore="false"></delivery></product>
</productcatalog>
</xml>
EOT;

		$result = Mf2\parse($input, 'http://www.staples.com/Swingline-747-Rio-Red-Stapler-20-Sheet-Capacity/product_562485');
		$this->assertCount(4, $result['items']);
	}

	/**
	 * @see https://github.com/indieweb/php-mf2/issues/81
	 */
	public function test_vevent() {
		$input = <<< EOT
<div class="vevent">
<h3 class="summary">XYZ Project Review</h3>
<p class="description">Project XYZ Review Meeting</p>
<p> <a class="url" href="http://example.com/xyz-meeting">http://example.com/xyz-meeting</a> </p>
<p>To be held on 
 <span class="dtstart">
  <abbr class="value" title="1998-03-12">the 12th of March</abbr> 
  from <span class="value">8:30am</span> <abbr class="value" title="-0500">EST</abbr>
 </span> until 
 <span class="dtend">
  <span class="value">9:30am</span> <abbr class="value" title="-0500">EST</abbr>
 </span>
</p>
<p>Location: <span class="location">1CP Conference Room 4350</span></p>
<small>Booked by: <span class="uid">guid-1.host1.com</span> on 
 <span class="dtstamp">
  <abbr class="value" title="1998-03-09">the 9th</abbr> at <span class="value">6:00pm</span>
 </span>
</small>
</div>
EOT;
		$parser = new Parser($input);
		$output = $parser->parse();

		$this->assertArrayHasKey('name', $output['items'][0]['properties']);
		$this->assertArrayHasKey('description', $output['items'][0]['properties']);
		$this->assertArrayHasKey('url', $output['items'][0]['properties']);
		$this->assertArrayHasKey('start', $output['items'][0]['properties']);
		$this->assertArrayHasKey('end', $output['items'][0]['properties']);

		$this->assertEquals('XYZ Project Review', $output['items'][0]['properties']['name'][0]);
		$this->assertEquals('Project XYZ Review Meeting', $output['items'][0]['properties']['description'][0]);
		$this->assertEquals('http://example.com/xyz-meeting', $output['items'][0]['properties']['url'][0]);
		$this->assertEquals('1998-03-12T08:30', $output['items'][0]['properties']['start'][0]);
		$this->assertEquals('1998-03-12T09:30', $output['items'][0]['properties']['end'][0]);
	}

}
