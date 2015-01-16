URL
===

Represents a URL, a library to be used in many other places.

Provides access to individual URL components, works correctly with absolute,
relative and protocol-relative variants.

Applies semantically-lossless normalisation for comparisons:

 * scheme and host case is ignored
 * percent-encoded entities are capitalised
 * decodes percent-encoded representations of unreserved characters
 * trailing / added to directory-ending URLs
 * default port removed
 * dot segments (/./ and /../) are removed
 * query string arguments are sorted by key name
 * IDN host names are normalised to the ASCII variant

Usage
-----

### Example of component access

```php
<?php
$url = new \webignition\Url\Url('https://github.com/webignition/url/');

$this->assertEquals('https', $url->getScheme());
$this->assertFalse($url->hasUser());
$this->assertFalse($url->hasQuery());
```

### Example of query normalisation

```php
<?php
$url = new \webignition\NormalisedUrl\NormalisedUrl('http://www.example.com?a=1&c=3&b=2');
$this->assertEquals('http://www.example.com/?a=1&b=2&c=3', (string)$url);

$url = new \webignition\Url('http://www.example.com?');
$this->assertEquals('http://www.example.com/', (string)$url);
```