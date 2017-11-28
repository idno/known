PHP Open Graph Library
======================

This is a very very simple PHP open graph parser.

Pass it the contents of a web page, and it'll spit back an associated array of open graph tags and the value.

Usage
-----

Include the library and call Parser's ::parse() function. 

Example:

```php

	require_once('ogp/Parser.php');

	$content = file_get_contents("https://www.youtube.com/watch?v=EIGGsZZWzZA");
	
	print_r(\ogp\Parser::parse($content));
```

Author
------

* Marcus Povey <marcus@marcus-povey.co.uk>

See
---

* Me <https://www.marcus-povey.co.uk>
* Open Graph <http://ogp.me>
