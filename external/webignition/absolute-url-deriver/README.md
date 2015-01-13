Absolute URL deriver [![Build Status](https://secure.travis-ci.org/webignition/absolute-url-deriver.png?branch=master)](http://travis-ci.org/webignition/absolute-url-deriver)
====================

Overview
---------

Derives absolute URL from relative and source URLs.

Useful when:

- extracting full HREF URLs for links in a HTML document
- determining absolute new URL for a 301 redirect where the HTTP server returned a relative Location value

Usage
-----

### The "Hello World" example

```php
<?php
$deriver = new \webignition\AbsoluteUrlDeriver\AbsoluteUrlDeriver(
    '/server.php?param1=value1',
    'http://www.example.com/pathOne/pathTwo/pathThree'
);

$this->assertEquals('http://www.example.com/server.php?param1=value1', $deriver->getAbsoluteUrl());
```

Building
--------

#### Using as a library in a project

If used as a dependency by another project, update that project's composer.json
and update your dependencies.

    "require": {
        "webignition/absolute-url-deriver": "*"      
    }

#### Developing

This project has external dependencies managed with [composer][3]. Get and install this first.

    # Make a suitable project directory
    mkdir ~/absolute-url-deriver && cd ~/absolute-url-deriver

    # Clone repository
    git clone git@github.com:webignition/absolute-url-deriver.git.

    # Retrieve/update dependencies
    composer.phar install

Testing
-------

Have look at the [project on travis][4] for the latest build status, or give the tests
a go yourself.

    cd ~/absolute-url-deriver
    phpunit tests


[3]: http://getcomposer.org
[4]: http://travis-ci.org/webignition/absolute-url-deriver/builds