<?php

namespace Mf2;

use DOMDocument;
use DOMElement;
use DOMXPath;
use DOMNode;
use DOMNodeList;
use Exception;
use SplObjectStorage;

/**
 * Parse Microformats2
 * 
 * Functional shortcut for the commonest cases of parsing microformats2 from HTML.
 * 
 * Example usage:
 * 
 *     use Mf2;
 *     $output = Mf2\parse('<span class="h-card">Barnaby Walters</span>');
 *     echo json_encode($output, JSON_PRETTY_PRINT);
 * 
 * Produces:
 * 
 *     {
 *      "items": [
 *       {
 *        "type": ["h-card"],
 *        "properties": {
 *         "name": ["Barnaby Walters"]
 *        }
 *       }
 *      ],
 *      "rels": {}
 *     }
 * 
 * @param string|DOMDocument $input The HTML string or DOMDocument object to parse
 * @param string $url The URL the input document was found at, for relative URL resolution
 * @param bool $convertClassic whether or not to convert classic microformats
 * @return array Canonical MF2 array structure
 */
function parse($input, $url = null, $convertClassic = true) {
	$parser = new Parser($input, $url);
	return $parser->parse($convertClassic);
}

/**
 * Unicode to HTML Entities
 * @param string $input String containing characters to convert into HTML entities
 * @return string 
 */
function unicodeToHtmlEntities($input) {
	return mb_convert_encoding($input, 'HTML-ENTITIES', mb_detect_encoding($input));
}

/**
 * Collapse Whitespace
 * 
 * Collapses any sequences of whitespace within a string into a single space
 * character.
 * 
 * @deprecated since v0.2.3
 * @param string $str
 * @return string
 */
function collapseWhitespace($str) {
	return preg_replace('/[\s|\n]+/', ' ', $str);
}

function unicodeTrim($str) {
	// this is cheating. TODO: find a better way if this causes any problems
	$str = str_replace(mb_convert_encoding('&nbsp;', 'UTF-8', 'HTML-ENTITIES'), ' ', $str);
	$str = preg_replace('/^\s+/', '', $str);
	return preg_replace('/\s+$/', '', $str);
}

/**
 * Microformat Name From Class string
 * 
 * Given the value of @class, get the relevant mf classnames (e.g. h-card, 
 * p-name).
 * 
 * @param string $class A space delimited list of classnames
 * @param string $prefix The prefix to look for
 * @return string|array The prefixed name of the first microfomats class found or false
 */
function mfNamesFromClass($class, $prefix = 'h-') {
	$classes = explode(' ', $class);
	$matches = array();

	foreach ($classes as $classname) {
		if (stristr(' ' . $classname, ' ' . $prefix) !== false) {
			$matches[] = ($prefix === 'h-') ? $classname : substr($classname, strlen($prefix));
		}
	}

	return $matches;
}

/**
 * Get Nested µf Property Name From Class
 * 
 * Returns all the p-, u-, dt- or e- prefixed classnames it finds in a 
 * space-separated string.
 * 
 * @param string $class
 * @return string|null
 */
function nestedMfPropertyNamesFromClass($class) {
	$prefixes = array(' p-', ' u-', ' dt-', ' e-');

	foreach (explode(' ', $class) as $classname) {
		foreach ($prefixes as $prefix) {
			if (stristr(' ' . $classname, $prefix))
				return mfNamesFromClass($classname, ltrim($prefix));
		}
	}

	return null;
}

/**
 * Wraps mfNamesFromClass to handle an element as input (common)
 * 
 * @param DOMElement $e The element to get the classname for
 * @param string $prefix The prefix to look for
 * @return mixed See return value of mf2\Parser::mfNameFromClass()
 */
function mfNamesFromElement(\DOMElement $e, $prefix = 'h-') {
	$class = $e->getAttribute('class');
	return mfNamesFromClass($class, $prefix);
}

/**
 * Wraps nestedMfPropertyNamesFromClass to handle an element as input
 */
function nestedMfPropertyNamesFromElement(\DOMElement $e) {
	$class = $e->getAttribute('class');
	return nestedMfPropertyNamesFromClass($class);
}

/**
 * Microformats2 Parser
 * 
 * A class which holds state for parsing microformats2 from HTML.
 * 
 * Example usage:
 * 
 *     use Mf2;
 *     $parser = new Mf2\Parser('<p class="h-card">Barnaby Walters</p>');
 *     $output = $parser->parse();
 */
class Parser {
	/** @var string The baseurl (if any) to use for this parse */
	public $baseurl;

	/** @var DOMXPath object which can be used to query over any fragment*/
	public $xpath;
	
	/** @var DOMDocument */
	public $doc;
	
	/** @var SplObjectStorage */
	protected $parsed;

	/**
	 * Constructor
	 * 
	 * @param DOMDocument|string $input The data to parse. A string of HTML or a DOMDocument
	 * @param string $url The URL of the parsed document, for relative URL resolution
	 */
	public function __construct($input, $url = null) {
		libxml_use_internal_errors(true);
		if (is_string($input)) {
			$doc = new DOMDocument();
			@$doc->loadHTML(unicodeToHtmlEntities($input));
		} elseif (is_a($input, 'DOMDocument')) {
			$doc = $input;
		} else {
			$doc = new DOMDocument();
			@$doc->loadHTML('');
		}
		
		$this->xpath = new DOMXPath($doc);
		
		$baseurl = $url;
		foreach ($this->xpath->query('//base[@href]') as $base) {
			$baseElementUrl = $base->getAttribute('href');
			
			if (parse_url($baseElementUrl, PHP_URL_SCHEME) === null) {
				/* The base element URL is relative to the document URL.
				 *
				 * :/
				 *
				 * Perhaps the author was high? */
				
				$baseurl = resolveUrl($url, $baseElementUrl);
			} else {
				$baseurl = $baseElementUrl;
			}
			break;
		}
		
		$this->baseurl = $baseurl;
		$this->doc = $doc;
		$this->parsed = new SplObjectStorage();
	}
	
	private function elementPrefixParsed(\DOMElement $e, $prefix) {
		if (!$this->parsed->contains($e))
			$this->parsed->attach($e, array());
		
		$prefixes = $this->parsed[$e];
		$prefixes[] = $prefix;
		$this->parsed[$e] = $prefixes;
	}
	
	private function isElementParsed(\DOMElement $e, $prefix) {
		if (!$this->parsed->contains($e))
			return false;
		
		$prefixes = $this->parsed[$e];
		
		if (!in_array($prefix, $prefixes))
			return false;
		
		return true;
	}
	
	// TODO: figure out if this has problems with sms: and geo: URLs
	public function resolveUrl($url) {
		// If the URL is seriously malformed it’s probably beyond the scope of this 
		// parser to try to do anything with it.
		if (parse_url($url) === false)
			return $url;
		
		$scheme = parse_url($url, PHP_URL_SCHEME);
		
		if (empty($scheme) and !empty($this->baseurl)) {
			return resolveUrl($this->baseurl, $url);
		} else {
			return $url;
		}
	}
	
	// Parsing Functions
	
	/**
	 * Parse value-class/value-title on an element, joining with $separator if 
	 * there are multiple.
	 * 
	 * @param \DOMElement $e
	 * @param string $separator = '' if multiple value-title elements, join with this string
	 * @return string|null the parsed value or null if value-class or -title aren’t in use
	 */
	public function parseValueClassTitle(\DOMElement $e, $separator = '') {
		$valueClassElements = $this->xpath->query('./*[contains(concat(" ", @class, " "), " value ")]', $e);
		
		if ($valueClassElements->length !== 0) {
			// Process value-class stuff
			$val = '';
			foreach ($valueClassElements as $el) {
				$val .= $el->textContent;
			}
			
			return unicodeTrim($val);
		}
		
		$valueTitleElements = $this->xpath->query('./*[contains(concat(" ", @class, " "), " value-title ")]', $e);
		
		if ($valueTitleElements->length !== 0) {
			// Process value-title stuff
			$val = '';
			foreach ($valueTitleElements as $el) {
				$val .= $el->getAttribute('title');
			}
			
			return unicodeTrim($val);
		}
		
		// No value-title or -class in this element
		return null;
	}
	
	/**
	 * Given an element with class="p-*", get it’s value
	 * 
	 * @param DOMElement $p The element to parse
	 * @return string The plaintext value of $p, dependant on type
	 * @todo Make this adhere to value-class
	 */
	public function parseP(\DOMElement $p) {
		$classTitle = $this->parseValueClassTitle($p, ' ');
		
		if ($classTitle !== null)
			return $classTitle;
		
		if ($p->tagName == 'img' and $p->getAttribute('alt') !== '') {
			$pValue = $p->getAttribute('alt');
		} elseif ($p->tagName == 'area' and $p->getAttribute('alt') !== '') {
			$pValue = $p->getAttribute('alt');
		} elseif ($p->tagName == 'abbr' and $p->getAttribute('title') !== '') {
			$pValue = $p->getAttribute('title');
		} elseif (in_array($p->tagName, array('data', 'input')) and $p->getAttribute('value') !== '') {
			$pValue = $p->getAttribute('value');
		} else {
			$pValue = unicodeTrim($p->textContent);
		}
		
		return $pValue;
	}

	/**
	 * Given an element with class="u-*", get the value of the URL
	 * 
	 * @param DOMElement $u The element to parse
	 * @return string The plaintext value of $u, dependant on type
	 * @todo make this adhere to value-class
	 */
	public function parseU(\DOMElement $u) {
		if (($u->tagName == 'a' or $u->tagName == 'area') and $u->getAttribute('href') !== null) {
			$uValue = $u->getAttribute('href');
		} elseif ($u->tagName == 'img' and $u->getAttribute('src') !== null) {
			$uValue = $u->getAttribute('src');
		} elseif ($u->tagName == 'object' and $u->getAttribute('data') !== null) {
			$uValue = $u->getAttribute('data');
		}
		
		if (isset($uValue)) {
			return $this->resolveUrl($uValue);
		}
		
		$classTitle = $this->parseValueClassTitle($u);
		
		if ($classTitle !== null) {
			return $classTitle;
		} elseif ($u->tagName == 'abbr' and $u->getAttribute('title') !== null) {
			return $u->getAttribute('title');
		} elseif (in_array($u->tagName, array('data', 'input')) and $u->getAttribute('value') !== null) {
			return $u->getAttribute('value');
		} else {
			return unicodeTrim($u->textContent);
		}
	}

	/**
	 * Given an element with class="dt-*", get the value of the datetime as a php date object
	 * 
	 * @param DOMElement $dt The element to parse
	 * @return string The datetime string found
	 */
	public function parseDT(\DOMElement $dt) {
		// Check for value-class pattern
		$valueClassChildren = $this->xpath->query('./*[contains(concat(" ", @class, " "), " value ") or contains(concat(" ", @class, " "), " value-title ")]', $dt);
		$dtValue = false;
		
		if ($valueClassChildren->length > 0) {
			// They’re using value-class
			$dateParts = array();
			
			foreach ($valueClassChildren as $e) {
				if (strstr(' ' . $e->getAttribute('class') . ' ', ' value-title ')) {
					$title = $e->getAttribute('title');
					if (!empty($title))
						$dateParts[] = $title;
				}
				elseif ($e->tagName == 'img' or $e->tagName == 'area') {
					// Use @alt
					$alt = $e->getAttribute('alt');
					if (!empty($alt))
						$dateParts[] = $alt;
				}
				elseif ($e->tagName == 'data') {
					// Use @value, otherwise innertext
					$value = $e->hasAttribute('value') ? $e->getAttribute('value') : unicodeTrim($e->nodeValue);
					if (!empty($value))
						$dateParts[] = $value;
				}
				elseif ($e->tagName == 'abbr') {
					// Use @title, otherwise innertext
					$title = $e->hasAttribute('title') ? $e->getAttribute('title') : unicodeTrim($e->nodeValue);
					if (!empty($title))
						$dateParts[] = $title;
				}
				elseif ($e->tagName == 'del' or $e->tagName == 'ins' or $e->tagName == 'time') {
					// Use @datetime if available, otherwise innertext
					$dtAttr = ($e->hasAttribute('datetime')) ? $e->getAttribute('datetime') : unicodeTrim($e->nodeValue);
					if (!empty($dtAttr))
						$dateParts[] = $dtAttr;
				}
				else {
					if (!empty($e->nodeValue))
						$dateParts[] = unicodeTrim($e->nodeValue);
				}
			}

			// Look through dateParts
			$datePart = '';
			$timePart = '';
			foreach ($dateParts as $part) {
				// Is this part a full ISO8601 datetime?
				if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}(?::\d{2})?(?:Z?[+|-]\d{2}:?\d{2})?$/', $part)) {
					// Break completely, we’ve got our value
					$dtValue = $part;
					break;
				} else {
					// Is the current part a valid time(+TZ?) AND no other time reprentation has been found?
					if ((preg_match('/\d{1,2}:\d{1,2}(Z?[+|-]\d{2}:?\d{2})?/', $part) or preg_match('/\d{1,2}[a|p]m/', $part)) and empty($timePart)) {
						$timePart = $part;
					} elseif (preg_match('/\d{4}-\d{2}-\d{2}/', $part) and empty($datePart)) {
						// Is the current part a valid date AND no other date reprentation has been found?
						$datePart = $part;
					}
					
					$dtValue = rtrim($datePart, 'T') . 'T' . unicodeTrim($timePart, 'T');
				}
			}
		} else {
			// Not using value-class (phew).
			if ($dt->tagName == 'img' or $dt->tagName == 'area') {
				// Use @alt
				// Is it an entire dt?
				$alt = $dt->getAttribute('alt');
				if (!empty($alt))
					$dtValue = $alt;
			} elseif (in_array($dt->tagName, array('data'))) {
				// Use @value, otherwise innertext
				// Is it an entire dt?
				$value = $dt->getAttribute('value');
				if (!empty($value))
					$dtValue = $value;
				else
					$dtValue = $dt->nodeValue;
			} elseif ($dt->tagName == 'abbr') {
				// Use @title, otherwise innertext
				// Is it an entire dt?
				$title = $dt->getAttribute('title');
				if (!empty($title))
					$dtValue = $title;
				else
					$dtValue = $dt->nodeValue;
			} elseif ($dt->tagName == 'del' or $dt->tagName == 'ins' or $dt->tagName == 'time') {
				// Use @datetime if available, otherwise innertext
				// Is it an entire dt?
				$dtAttr = $dt->getAttribute('datetime');
				if (!empty($dtAttr))
					$dtValue = $dtAttr;
				else
					$dtValue = $dt->nodeValue;
			} else {
				$dtValue = $dt->nodeValue;
			}
		}

		return $dtValue;
	}

	/**
	 * 	Given the root element of some embedded markup, return a string representing that markup
	 *
	 * 	@param DOMElement $e The element to parse
	 * 	@return string $e’s innerHTML
	 * 
	 * @todo need to mark this element as e- parsed so it doesn’t get parsed as it’s parent’s e-* too
	 */
	public function parseE(\DOMElement $e) {
		$classTitle = $this->parseValueClassTitle($e);
		
		if ($classTitle !== null)
			return $classTitle;
		
		// Expand relative URLs within children of this element
		// TODO: as it is this is not relative to only children, make this .// and rerun tests
		$hyperlinkChildren = $this->xpath->query('//*[@src or @href or @data]', $e);
		
		foreach ($hyperlinkChildren as $child) {
			if ($child->hasAttribute('href'))
				$child->setAttribute('href', $this->resolveUrl($child->getAttribute('href')));
			if ($child->hasAttribute('src'))
				$child->setAttribute('src', $this->resolveUrl($child->getAttribute('src')));
			if ($child->hasAttribute('data'))
				$child->setAttribute('data', $this->resolveUrl($child->getAttribute('data')));
		}
		
		$html = '';
		foreach ($e->childNodes as $node) {
			$html .= $node->C14N();
		}
		
		return array(
			'html' => $html,
			'value' => unicodeTrim($e->textContent)
		);
	}

	/**
	 * Recursively parse microformats
	 * 
	 * @param DOMElement $e The element to parse
	 * @return array A representation of the values contained within microformat $e
	 */
	public function parseH(\DOMElement $e) {
		// If it’s already been parsed (e.g. is a child mf), skip
		if ($this->parsed->contains($e))
			return null;

		// Get current µf name
		$mfTypes = mfNamesFromElement($e, 'h-');

		// Initalise var to store the representation in
		$return = array();
		$children = array();

		// Handle nested microformats (h-*)
		foreach ($this->xpath->query('.//*[contains(concat(" ", @class)," h-")]', $e) as $subMF) {
			// Parse
			$result = $this->parseH($subMF);
			
			// If result was already parsed, skip it
			if (null === $result)
				continue;
			
			$result['value'] = $this->parseP($subMF);

			// Does this µf have any property names other than h-*?
			$properties = nestedMfPropertyNamesFromElement($subMF);
			
			if (!empty($properties)) {
				// Yes! It’s a nested property µf
				foreach ($properties as $property) {
					$return[$property][] = $result;
				}
			} else {
				// No, it’s a child µf
				$children[] = $result;
			}
			
			// Make sure this sub-mf won’t get parsed as a µf or property
			// TODO: Determine if clearing this is required?
			$this->elementPrefixParsed($subMF, 'h');
			$this->elementPrefixParsed($subMF, 'p');
			$this->elementPrefixParsed($subMF, 'u');
			$this->elementPrefixParsed($subMF, 'dt');
			$this->elementPrefixParsed($subMF, 'e');
		}

		// Handle p-*
		foreach ($this->xpath->query('.//*[contains(concat(" ", @class) ," p-")]', $e) as $p) {
			if ($this->isElementParsed($p, 'p'))
				continue;

			$pValue = $this->parseP($p);
			
			// Add the value to the array for it’s p- properties
			foreach (mfNamesFromElement($p, 'p-') as $propName) {
				if (!empty($propName))
					$return[$propName][] = $pValue;
			}
			
			// Make sure this sub-mf won’t get parsed as a top level mf
			$this->elementPrefixParsed($p, 'p');
		}

		// Handle u-*
		foreach ($this->xpath->query('.//*[contains(concat(" ",  @class)," u-")]', $e) as $u) {
			if ($this->isElementParsed($u, 'u'))
				continue;
			
			$uValue = $this->parseU($u);
			
			// Add the value to the array for it’s property types
			foreach (mfNamesFromElement($u, 'u-') as $propName) {
				$return[$propName][] = $uValue;
			}
			
			// Make sure this sub-mf won’t get parsed as a top level mf
			$this->elementPrefixParsed($u, 'u');
		}
		
		// Handle dt-*
		foreach ($this->xpath->query('.//*[contains(concat(" ", @class), " dt-")]', $e) as $dt) {
			if ($this->isElementParsed($dt, 'dt'))
				continue;
			
			$dtValue = $this->parseDT($dt);
			
			if ($dtValue) {
				// Add the value to the array for dt- properties
				foreach (mfNamesFromElement($dt, 'dt-') as $propName) {
					$return[$propName][] = $dtValue;
				}
			}
			
			// Make sure this sub-mf won’t get parsed as a top level mf
			$this->elementPrefixParsed($dt, 'dt');
		}

		// Handle e-*
		foreach ($this->xpath->query('.//*[contains(concat(" ", @class)," e-")]', $e) as $em) {
			if ($this->isElementParsed($em, 'e'))
				continue;

			$eValue = $this->parseE($em);

			if ($eValue) {
				// Add the value to the array for e- properties
				foreach (mfNamesFromElement($em, 'e-') as $propName) {
					$return[$propName][] = $eValue;
				}
			}
			// Make sure this sub-mf won’t get parsed as a top level mf
			$this->elementPrefixParsed($em, 'e');
		}

		// Implied Properties
		// Check for p-name
		if (!array_key_exists('name', $return)) {
			try {
				// Look for img @alt
				if ($e->tagName == 'img' and $e->getAttribute('alt') != '')
					throw new Exception($e->getAttribute('alt'));
				
				if ($e->tagName == 'abbr' and $e->hasAttribute('title'))
					throw new Exception($e->getAttribute('title'));
				
				// Look for nested img @alt
				foreach ($this->xpath->query('./img[count(preceding-sibling::*)+count(following-sibling::*)=0]', $e) as $em) {
					if ($em->getAttribute('alt') != '')
						throw new Exception($em->getAttribute('alt'));
				}

				// Look for double nested img @alt
				foreach ($this->xpath->query('./*[count(preceding-sibling::*)+count(following-sibling::*)=0]/img[count(preceding-sibling::*)+count(following-sibling::*)=0]', $e) as $em) {
					if ($em->getAttribute('alt') != '')
						throw new Exception($em->getAttribute('alt'));
				}

				throw new Exception($e->nodeValue);
			} catch (Exception $exc) {
				$return['name'][] = unicodeTrim($exc->getMessage());
			}
		}

		// Check for u-photo
		if (!array_key_exists('photo', $return)) {
			// Look for img @src
			try {
				if ($e->tagName == 'img')
					throw new Exception($e->getAttribute('src'));

				// Look for nested img @src
				foreach ($this->xpath->query('./img[count(preceding-sibling::*)+count(following-sibling::*)=0]', $e) as $em) {
					if ($em->getAttribute('src') != '')
						throw new Exception($em->getAttribute('src'));
				}

				// Look for double nested img @src
				foreach ($this->xpath->query('./*[count(preceding-sibling::*)+count(following-sibling::*)=0]/img[count(preceding-sibling::*)+count(following-sibling::*)=0]', $e) as $em) {
					if ($em->getAttribute('src') != '')
						throw new Exception($em->getAttribute('src'));
				}
			} catch (Exception $exc) {
				$return['photo'][] = $this->resolveUrl($exc->getMessage());
			}
		}

		// Check for u-url
		if (!array_key_exists('url', $return)) {
			// Look for img @src
			if ($e->tagName == 'a')
				$url = $e->getAttribute('href');
			
			// Look for nested img @src
			foreach ($this->xpath->query('./a[count(preceding-sibling::a)+count(following-sibling::a)=0]', $e) as $em) {
				$url = $em->getAttribute('href');
				break;
			}
			
			if (!empty($url))
				$return['url'][] = $this->resolveUrl($url);
		}

		// Make sure things are in alphabetical order
		sort($mfTypes);
		
		// Phew. Return the final result.
		$parsed = array(
			'type' => $mfTypes,
			'properties' => $return
		);
		if (!empty($children))
			$parsed['children'] = array_values(array_filter($children));
		return $parsed;
	}
	
	public function parseRelsAndAlternates() {
		$rels = array();
		$alternates = array();
		
		// Iterate through all a, area and link elements with rel attributes
		foreach ($this->xpath->query('//*[@rel and @href]') as $hyperlink) {
			if ($hyperlink->getAttribute('rel') == '')
				continue;
			
			// Resolve the href
			$href = $this->resolveUrl($hyperlink->getAttribute('href'));
			
			// Split up the rel into space-separated values
			$linkRels = array_filter(explode(' ', $hyperlink->getAttribute('rel')));
			
			// If alternate in rels, create alternate structure, append
			if (in_array('alternate', $linkRels)) {
				$alt = array(
					'url' => $href,
					'rel' => implode(' ', array_diff($linkRels, array('alternate')))
				);
				if ($hyperlink->hasAttribute('media'))
					$alt['media'] = $hyperlink->getAttribute('media');
				
				if ($hyperlink->hasAttribute('hreflang'))
					$alt['hreflang'] = $hyperlink->getAttribute('hreflang');
				
				$alternates[] = $alt;
			} else {
				foreach ($linkRels as $rel) {
					$rels[$rel][] = $href;
				}
			}
		}
		
		if (count($rels) === 0)
			$rels = new \stdclass;
		
		return array($rels, $alternates);
	}
	
	/**
	 * Kicks off the parsing routine
	 * 
	 * If `$htmlSafe` is set, any angle brackets in the results from non e-* properties
	 * will be HTML-encoded, bringing all output to the same level of encoding.
	 * 
	 * If a DOMElement is set as the $context, only descendants of that element will
	 * be parsed for microformats.
	 * 
	 * @param bool $htmlSafe whether or not to html-encode non e-* properties. Defaults to false
	 * @param DOMElement $context optionally an element from which to parse microformats
	 * @return array An array containing all the µfs found in the current document
	 */
	public function parse($convertClassic = true, DOMElement $context = null) {
		$mfs = array();
		
		if ($convertClassic) {
			$this->convertLegacy();
		}
		
		$mfElements = null === $context
			? $this->xpath->query('//*[contains(concat(" ",	@class), " h-")]')
			: $this->xpath->query('.//*[contains(concat(" ",	@class), " h-")]', $context);
		
		// Parser microformats
		foreach ($mfElements as $node) {
			// For each microformat
			$result = $this->parseH($node);

			// Add the value to the array for this property type
			$mfs[] = $result;
		}
		
		// Parse rels
		list($rels, $alternates) = $this->parseRelsAndAlternates();
		
		$top = array(
			'items' => array_values(array_filter($mfs)),
			'rels' => $rels
		);
		
		if (count($alternates))
			$top['alternates'] = $alternates;
		
		return $top;
	}
	
	/**
	 * Parse From ID
	 * 
	 * Given an ID, parse all microformats which are children of the element with
	 * that ID.
	 * 
	 * Note that rel values are still document-wide.
	 * 
	 * If an element with the ID is not found, an empty skeleton mf2 array structure 
	 * will be returned.
	 * 
	 * @param string $id
	 * @param bool $htmlSafe = false whether or not to HTML-encode angle brackets in non e-* properties
	 * @return array
	 */
	public function parseFromId($id, $convertClassic=true) {
		$matches = $this->xpath->query("//*[@id='{$id}']");
		
		if (empty($matches))
			return array('items' => array(), 'rels' => array(), 'alternates' => array());
		
		return $this->parse($convertClassic, $matches->item(0));
	}

	/**
	 * Convert Legacy Classnames
	 * 
	 * Adds microformats2 classnames into a document containing only legacy
	 * semantic classnames.
	 * 
	 * @return Parser $this
	 */
	public function convertLegacy() {
		$doc = $this->doc;
		$xp = new DOMXPath($doc);
		
		// replace all roots
		foreach ($this->classicRootMap as $old => $new) {
			foreach ($xp->query('//*[contains(concat(" ", @class, " "), " ' . $old . ' ") and not(contains(concat(" ", @class, " "), " ' . $new . ' "))]') as $el) {
				$el->setAttribute('class', $el->getAttribute('class') . ' ' . $new);
			}
		}
		
		foreach ($this->classicPropertyMap as $oldRoot => $properties) {
			$newRoot = $this->classicRootMap[$oldRoot];
			foreach ($properties as $old => $new) {
				foreach ($xp->query('//*[contains(concat(" ", @class, " "), " ' . $oldRoot . ' ")]//*[contains(concat(" ", @class, " "), " ' . $old . ' ") and not(contains(concat(" ", @class, " "), " ' . $new . ' "))]') as $el) {
					$el->setAttribute('class', $el->getAttribute('class') . ' ' . $new);
				}
			}
		}
		
		return $this;
	}
	
	/**
	 * XPath Query
	 * 
	 * Runs an XPath query over the current document. Works in exactly the same
	 * way as DOMXPath::query.
	 * 
	 * @param string $expression
	 * @param DOMNode $context
	 * @return DOMNodeList
	 */
	public function query($expression, $context = null) {
		return $this->xpath->query($expression, $context);
	}
	
	/**
	 * Classic Root Classname map
	 */
	public $classicRootMap = array(
		'vcard' => 'h-card',
		'hfeed' => 'h-feed',
		'hentry' => 'h-entry',
		'hrecipe' => 'h-recipe',
		'hresume' => 'h-resume',
		'hevent' => 'h-event',
		'hreview' => 'h-review'
	);
	
	public $classicPropertyMap = array(
		'vcard' => array(
			'fn' => 'p-name',
			'url' => 'u-url',
			'honorific-prefix' => 'p-honorific-prefix',
			'given-name' => 'p-given-name',
			'additional-name' => 'p-additional-name',
			'family-name' => 'p-family-name',
			'honorific-suffix' => 'p-honorific-suffix',
			'nickname' => 'p-nickname',
			'email' => 'u-email',
			'logo' => 'u-logo',
			'photo' => 'u-photo',
			'url' => 'u-url',
			'uid' => 'u-uid',
			'category' => 'p-category',
			'adr' => 'p-adr h-adr',
			'extended-address' => 'p-extended-address',
			'street-address' => 'p-street-address',
			'locality' => 'p-locality',
			'region' => 'p-region',
			'postal-code' => 'p-postal-code',
			'country-name' => 'p-country-name',
			'label' => 'p-label',
			'geo' => 'p-geo h-geo',
			'latitude' => 'p-latitude',
			'longitude' => 'p-longitude',
			'tel' => 'p-tel',
			'note' => 'p-note',
			'bday' => 'dt-bday',
			'key' => 'u-key',
			'org' => 'p-org',
			'organization-name' => 'p-organization-name',
			'organization-unit' => 'p-organization-unit',
		),
		'hentry' => array(
			'entry-title' => 'p-name',
			'entry-summary' => 'p-summary',
			'entry-content' => 'e-content',
			'published' => 'dt-published',
			'updated' => 'dt-updated',
			'author' => 'p-author h-card',
			'category' => 'p-category',
			'geo' => 'p-geo h-geo',
			'latitude' => 'p-latitude',
			'longitude' => 'p-longitude',
		),
		'hrecipe' => array(
			'fn' => 'p-name',
			'ingredient' => 'p-ingredient',
			'yield' => 'p-yield',
			'instructions' => 'e-instructions',
			'duration' => 'dt-duration',
			'nutrition' => 'p-nutrition',
			'photo' => 'u-photo',
			'summary' => 'p-summary',
			'author' => 'p-author h-card'
		),
		'hresume' => array(
			'summary' => 'p-summary',
			'contact' => 'h-card p-contact',
			'education' => 'h-event p-education',
			'experience' => 'h-event p-experience',
			'skill' => 'p-skill',
			'affiliation' => 'p-affiliation h-card',
		),
		'hevent' => array(
			'dtstart' => 'dt-start',
			'dtend' => 'dt-end',
			'duration' => 'dt-duration',
			'description' => 'p-description',
			'summary' => 'p-summary',
			'description' => 'p-description',
			'url' => 'u-url',
			'category' => 'p-category',
			'location' => 'h-card',
			'geo' => 'p-location h-geo'
		),
		'hreview' => array(
			'summary' => 'p-name',
			'fn' => 'p-item h-item p-name', // doesn’t work properly, see spec
			'photo' => 'u-photo', // of the item being reviewed (p-item h-item u-photo)
			'url' => 'u-url', // of the item being reviewed (p-item h-item u-url)
			'reviewer' => 'p-reviewer p-author h-card',
			'dtreviewed' => 'dt-reviewed',
			'rating' => 'p-rating',
			'best' => 'p-best',
			'worst' => 'p-worst',
			'description' => 'p-description'
		)
	);
}

function parseUriToComponents($uri) {
	$result = array(
		'scheme' => null,
		'authority' => null,
		'path' => null,
		'query' => null,
		'fragment' => null
	);

	$u = @parse_url($uri);

	if(array_key_exists('scheme', $u))
		$result['scheme'] = $u['scheme'];

	if(array_key_exists('host', $u)) {
		if(array_key_exists('user', $u))
			$result['authority'] = $u['user'];
		if(array_key_exists('pass', $u))
			$result['authority'] .= ':' . $u['pass'];
		if(array_key_exists('user', $u) || array_key_exists('pass', $u))
			$result['authority'] .= '@';
		$result['authority'] .= $u['host'];
	}

	if(array_key_exists('path', $u))
		$result['path'] = $u['path'];

	if(array_key_exists('query', $u))
		$result['query'] = $u['query'];

	if(array_key_exists('fragment', $u))
		$result['fragment'] = $u['fragment'];

	return $result;
}

function resolveUrl($baseURI, $referenceURI) {
	$target = array(
		'scheme' => null,
		'authority' => null,
		'path' => null,
		'query' => null,
		'fragment' => null
	);

	# 5.2.1 Pre-parse the Base URI
	# The base URI (Base) is established according to the procedure of
  # Section 5.1 and parsed into the five main components described in
  # Section 3
	$base = parseUriToComponents($baseURI);

	# If base path is blank (http://example.com) then set it to /
	# (I can't tell if this is actually in the RFC or not, but seems like it makes sense)
	if($base['path'] == null)
		$base['path'] = '/';

	# 5.2.2. Transform References

	# The URI reference is parsed into the five URI components
	# (R.scheme, R.authority, R.path, R.query, R.fragment) = parse(R);
	$reference = parseUriToComponents($referenceURI);

	# A non-strict parser may ignore a scheme in the reference
	# if it is identical to the base URI's scheme.
	# TODO

	if($reference['scheme']) {
		$target['scheme'] = $reference['scheme'];
		$target['authority'] = $reference['authority'];
		$target['path'] = removeDotSegments($reference['path']);
		$target['query'] = $reference['query'];
	} else {
		if($reference['authority']) {
			$target['authority'] = $reference['authority'];
			$target['path'] = removeDotSegments($reference['path']);
			$target['query'] = $reference['query'];
		} else {
			if($reference['path'] == '') {
				$target['path'] = $base['path'];
				if($reference['query']) {
					$target['query'] = $reference['query'];
				} else {
					$target['query'] = $base['query'];
				}
			} else {
				if(substr($reference['path'], 0, 1) == '/') {
					$target['path'] = removeDotSegments($reference['path']);
				} else {
					$target['path'] = mergePaths($base, $reference);
					$target['path'] = removeDotSegments($target['path']);
				}
				$target['query'] = $reference['query'];
			}
			$target['authority'] = $base['authority'];
		}
		$target['scheme'] = $base['scheme'];
	}
	$target['fragment'] = $reference['fragment'];

	# 5.3 Component Recomposition
	$result = '';
	if($target['scheme']) {
		$result .= $target['scheme'] . ':';
	}
	if($target['authority']) {
		$result .= '//' . $target['authority'];
	}
	$result .= $target['path'];
	if($target['query']) {
		$result .= '?' . $target['query'];
	}
	if($target['fragment']) {
		$result .= '#' . $target['fragment'];
	} elseif($referenceURI == '#') {
		$result .= '#';
	}
	return $result;
}

# 5.2.3 Merge Paths
function mergePaths($base, $reference) {
	# If the base URI has a defined authority component and an empty
	#    path, 
	if($base['authority'] && $base['path'] == null) {
		# then return a string consisting of "/" concatenated with the
		# reference's path; otherwise,
		$merged = '/' . $reference['path'];
	} else {
		if(($pos=strrpos($base['path'], '/')) !== false) {
			# return a string consisting of the reference's path component
			#    appended to all but the last segment of the base URI's path (i.e.,
			#    excluding any characters after the right-most "/" in the base URI
			#    path,
			$merged = substr($base['path'], 0, $pos + 1) . $reference['path'];
		} else {
			#    or excluding the entire base URI path if it does not contain
			#    any "/" characters).
			$merged = $base['path'];
		}
	}
	return $merged;
}

# 5.2.4.A Remove leading ../ or ./
function removeLeadingDotSlash(&$input) {
	if(substr($input, 0, 3) == '../') {
		$input = substr($input, 3);
	} elseif(substr($input, 0, 2) == './') {
		$input = substr($input, 2);
	}
}

# 5.2.4.B Replace leading /. with /
function removeLeadingSlashDot(&$input) {
	if(substr($input, 0, 3) == '/./') {
		$input = '/' . substr($input, 3);
	} else {
		$input = '/' . substr($input, 2);
	}
}

# 5.2.4.C Given leading /../ remove component from output buffer
function removeOneDirLevel(&$input, &$output) {
	if(substr($input, 0, 4) == '/../') {
		$input = '/' . substr($input, 4);
	} else {
		$input = '/' . substr($input, 3);
	}
	$output = substr($output, 0, strrpos($output, '/'));
}

# 5.2.4.D Remove . and .. if it's the only thing in the input
function removeLoneDotDot(&$input) {
	if($input == '.') {
		$input = substr($input, 1);
	} else {
		$input = substr($input, 2);
	}
}

# 5.2.4.E Move one segment from input to output
function moveOneSegmentFromInput(&$input, &$output) {
	if(substr($input, 0, 1) != '/') {
		$pos = strpos($input, '/');
	} else {
		$pos = strpos($input, '/', 1);
	}

	if($pos === false) {
		$output .= $input;
		$input = '';
	} else {
		$output .= substr($input, 0, $pos);
		$input = substr($input, $pos);
	}
}

# 5.2.4 Remove Dot Segments
function removeDotSegments($path) {
	# 1.  The input buffer is initialized with the now-appended path
	#     components and the output buffer is initialized to the empty
	#     string.
	$input = $path;
	$output = '';

	$step = 0;

	# 2.  While the input buffer is not empty, loop as follows:
	while($input) {
		$step++;

		if(substr($input, 0, 3) == '../' || substr($input, 0, 2) == './') {
			#     A.  If the input buffer begins with a prefix of "../" or "./",
			#         then remove that prefix from the input buffer; otherwise,
			removeLeadingDotSlash($input);
		} elseif(substr($input, 0, 3) == '/./' || $input == '/.') {
			#     B.  if the input buffer begins with a prefix of "/./" or "/.",
			#         where "." is a complete path segment, then replace that
			#         prefix with "/" in the input buffer; otherwise,
			removeLeadingSlashDot($input);
		} elseif(substr($input, 0, 4) == '/../' || $input == '/..') {
			#     C.  if the input buffer begins with a prefix of "/../" or "/..",
			#          where ".." is a complete path segment, then replace that
			#          prefix with "/" in the input buffer and remove the last
			#          segment and its preceding "/" (if any) from the output
			#          buffer; otherwise,
			removeOneDirLevel($input, $output);
		} elseif($input == '.' || $input == '..') {
			#     D.  if the input buffer consists only of "." or "..", then remove
			#         that from the input buffer; otherwise,
			removeLoneDotDot($input);
		} else {
			#     E.  move the first path segment in the input buffer to the end of
			#         the output buffer and any subsequent characters up to, but not including,
			#         the next "/" character or the end of the input buffer
			moveOneSegmentFromInput($input, $output);
		}
	}

	return $output;
}
