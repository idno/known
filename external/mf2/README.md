php-mf2
=======

php-mf2 is a pure, generic [microformats-2](http://microformats.org/wiki/microformats-2) parser. It makes HTML as easy to consume as JSON.

Instead of having a hard-coded list of all the different microformats, it follows a set of procedures to handle different property types (e.g. `p-` for plaintext, `u-` for URL, etc). This allows for a very small and maintainable parser.

## Installation

Install php-mf2 with [Composer](http://getcomposer.org) by adding `"mf2/mf2": "0.2.*"` to the `require` object in your `composer.json` and running <kbd>php composer.phar update</kbd>.

You could install it by just downloading `/Mf2/Parser.php` and including that, but please use Composer. Seriously, it’s amazing.

## Usage

mf2 is PSR-0 autoloadable, so all you have to do to load it is:

1. Include Composer’s auto-generated autoload file (`/vendor/autoload.php`)
1. Call `Mf2\parse()` with the HTML (or a DOMDocument), and optionally the URL to resolve relative URLs against.

## Examples

### Parsing implied microformats2

```php
<?php

namespace YourApp;

require '/vendor/autoload.php';

use Mf2;

$output = Mf2\parse('<p class="h-card">Barnaby Walters</p>');
```

`$output` is a canonical microformats2 array structure like:

```json
{
	"items": [{
		"type": ["h-card"],
		"properties": {
			"name": ["Barnaby Walters"]
		}
	}],
	"rels": {}
}
```

If no microformats are found, `items` will be an empty array.

Note that, whilst the property prefixes are stripped, the prefix of the `h-*` classname(s) in the "type" array are left on.

### Parsing a document with relative URLs

Most of the time you’ll be getting your input HTML from a URL. You should pass that URL as the second parameter to `Mf2\parse()` so that any relative URLs in the document can be resolved. For example, say you got the following HTML from `http://example.com/`:

```html
<div class="h-card">
	<h1 class="p-name">Mr. Example</h1>
	<img class="u-photo" alt="" src="photo.png" />
</div>
```

Parsing like this:

```php
$output = Mf2\parse($html, 'http://example.org');
```

will result in the following output, with relative URLs made absolute:

```json
{
	"items": [{
		"type": ["h-card"],
		"properties": {
			"photo": ["http://example.org/photo.png"]
		}
	}],
	"rels": {}
}
```

php-mf2 correctly handles relative URL resolution according to the URI and HTML specs, including correct use of the `<base>` element.

### Parsing `rel` and `rel=alternate` values

php-mf2 also parses any link relations in the document, placing them into two top-level arrays — one for `rel=alternate` and another for all other rel values, e.g. when parsing:

```html
<a rel="me" href="https://twitter.com/barnabywalters">Me on twitter</a>
<link rel="alternate etc" href="http://example.com/notes.atom" />
```

parsing will result in the following keys:

```json
{
	"items": [],
	"rels": {
		"me": ["https://twitter.com/barnabywalters"]
	},
	"alternates": [{
		"url": "http://example.com/notes.atom",
		"rel": "etc"
	}]
}
```

Protip: if you’re not bothered about the microformats2 data and just want rels and alternates, you can improve performance by creating a `Mf2\Parser` object (see below) and calling `->parseRelsAndAlternates()` instead of `->parse()`, e.g.

```php
<?php

use Mf2;

$parser = new Mf2\Parser('<link rel="…');
$relsAndAlternates = $parser->parseRelsAndAlternates();
```

### Getting more control by creating a Parser object

The `Mf2\parse()` function covers the most common usage patterns by internally creating an instance of `Mf2\Parser` and returning the output all in one step. For some advanced usage you can also create an instance of `Mf2\Parser` yourself.

The constructor takes two arguments, the input HTML (or a DOMDocument) and the URL to use as a base URL. Once you have a parser, there are a few other things you can do:

### Selectively parsing a document

There are several ways to selectively parse microformats from a document. If you wish to only parse microformats from an element with a particular ID, `Parser::parseFromId($id) ` is the easiest way.

If your needs are more complex, `Parser::parse` accepts an optional context DOMNode as its second parameter. Typically you’d use `Parser::query` to run XPath queries on the document to get the element you want to parse from under, then pass it to `Parser::parse`. Example usage:

```php
$doc = 'More microformats, more microformats <div id="parse-from-here"><span class="h-card">This shows up</span></div> yet more ignored content';
$parser = new Mf2\Parser($doc);

$parser->parseFromId('parse-from-here'); // returns a document with only the h-card descended from div#parse-from-here

$elementIWant = $parser->query('an xpath query')[0];

$parser->parse(true, $elementIWant); // returns a document with only mfs under the selected element

```

### Classic Microformats Markup

php-mf2 has some support for parsing classic microformats markup. It’s enabled by default, but can be turned off by calling `Mf2\parse($html, $url, false);` or `$parser->parse(false);` if you’re instanciating a parser yourself.

In previous versions of php-mf2 you could also add your own class mappings — officially this is no longer supported.

* If the built in mappings don’t successfully parse some classic microformats markup then raise an issue and we’ll fix it.
* If you want to screen-scrape websites which don’t use mf2 into mf2 data structures, consider contributing to [php-mf2-shim](https://github.com/indieweb/php-mf2-shim)
* If you *really* need to make one-off changes to the default mappings… It is possible. But you have to figure it out for yourself ;)

## Security

**No filtering of content takes place in mf2\Parser, so treat its output as you would any untrusted data from the source of the parsed document.**

Some tips:

* All content apart from the 'html' key in dictionaries produced by parsing an `e-*` property is not HTML-escaped. For example, `<span class="p-name">&lt;code&gt;</span>` will result in `"name": ["<code>"]`. At the very least, HTML-escape all properties before echoing them out in HTML
* If you’re using the raw HTML content under the 'html' key of dictionaries produced by parsing `e-*` properties, you SHOULD purify the HTML before displaying it to prevent injection of arbitrary code. For PHP I recommend using [HTML Purifier](http://htmlpurifier.org)

TODO: move this section to a security/consumption best practises page on the wiki

## Contributing

Pull requests very welcome, please try to maintain stylistic, structural and naming consistency with the existing codebase, and don’t be too upset if I make naming changes :)

Please add tests which cover changes you plan to make or have made. I use PHPUnit, which is the de-facto standard for modern PHP development.

At the very least, run the test suite before and after making your changes to make sure you haven’t broken anything.

Issues/bug reports welcome. If you know how to write tests then please do so as code always expresses problems and intent much better than English, and gives me a way of measuring whether or not fixes have actually solved your problem. If you don’t know how to write tests, don’t worry :) Just include as much useful information in the issue as you can.

## Testing

Tests are written in phpunit and are contained within `/tests/`. Running <kbd>bin/phpunit</kbd> from the root dir will run them all.

There are enough tests to warrant putting them into separate suites for maintenance. They should be fairly self-explanatory.

php-mf2 can also be hooked up to the official, cross-platform [microformats2 test suite](https://github.com/microformats/tests). TODO: write a guide on how to do this, make a public endpoint for people to look at the results

### Changelog

#### v0.2.3

* Made p-* parsing consistent with implied name parsing
* Stopped collapsing whitespace in p-* properties
* Implemented unicodeTrim which removes &nbsp; characters as well as regex \s
* Added support for implied name via abbr[title]
* Prevented excessively nested value-class elements from being parsed incorrectly, removed incorrect separator which was getting added in some cases
* Updated u-* parsing to be spec-compliant, matching [href] before value-class and only attempting URL resolution for URL attributes
* Added support for input[value] parsing
* Tests for all the above

#### v0.2.2

* Made resolveUrl method public, allowing advanced parsers and subclasses to make use of it
* Fixed bug causing multiple duplicate property values to appear

#### v0.2.1

* Fixed bug causing classic microformats property classnames to not be parsed correctly

#### v0.2.0 (BREAKING CHANGES)

* Namespace change from mf2 to Mf2, for PSR-0 compatibility
* `Mf2\parse()` function added to simplify the most common case of just parsing some HTML
* Updated e-* property parsing rules to match mf2 parsing spec — instead of producing inconsistent HTML content, it now produces dictionaries like <pre><code>
{
	"html": "<b>The Content</b>",
	"value: "The Content"
}
</code></pre>
* Removed `htmlSafe` options as new e-* parsing rules make them redundant
* Moved a whole load of static functions out of the class and into standalone functions
* Changed autoloading to always include Parser.php instead of using classmap

#### v0.1.23

* Made some changes to the way back-compatibility with classic microformats are handled, ignoring classic property classnames inside mf2 roots and outside classic roots
* Deprecated ability to add new classmaps, removed twitter classmap. Use [php-mf2-shim](http://github.com/indieweb/php-mf2-shim) instead, it’s better

#### v0.1.22

* Converts classic microformats by default

#### v0.1.21

* Removed webignition dependency, also removing ext-intl dependency. php-mf2 is now a standalone, single file library again
* Replaced webignition URL resolving with custom code passing almost all tests, courtesy of <a class="h-card" href="http://aaronparecki.com">Aaron Parecki</a>

#### v0.1.20

* Added in almost-perfect custom URL resolving code

#### v0.1.19 (2013-06-11)

* Required stable version of webigniton/absolute-url-resolver, hopefully resolving versioning problems

#### v0.1.18 (2013-06-05)

* Fixed problems with isElementParsed, causing elements to be incorrectly parsed
* Cleaned up some test files

#### v0.1.17

* Rewrote some PHP 5.4 array syntax which crept into 0.1.16 so php-mf2 still works on PHP 5.3
* Fixed a bug causing weird partial microformats to be added to parent microformats if they had doubly property-nested children
* Finally actually licensed this project under a real license (MIT, in composer.json)
* Suggested barnabywalters/mf-cleaner in composer.json

#### v0.1.16

* Ability to parse from only an ID
* Context DOMElement can be passed to $parse
* Parser::query runs XPath queries on the current document
* When parsing e-* properties, elements with @src, @data or @href have relative URLs resolved in the output

#### v0.1.15

* Added html-safe options
* Added rel+rel-alternate parsing