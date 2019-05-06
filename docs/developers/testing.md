# Unit Testing

Known has a number of PHP unit tests that can be run in order to check that things are working correctly, and to protect against any code regression.

## Running from Github

Unit tests will automatically be run when you submit a pull request on GitHub. You should rectify any errors which are reported, as pull requests with errors will not be merged.

## Running unit tests on your local machine

First, ensure [PHPUnit](https://phpunit.de/) is installed. (Installation varies by system and is beyond the scope of this documentation.)

To run all the tests, from the Known root directory simply run ```phpunit```.

Some tests require a connection to your local Known instance, so if this is anything other than *http://localhost*, you need to set the ```KNOWN_DOMAIN``` environment variable.

Before running the unit test, set this by running ```export KNOWN_DOMAIN='MY.KNOWN.DOMAIN'```

# Code style testing

Known includes a PHP Codesniffer ruleset.

## Running style tests on your local machine

First, make sure you've got the known phpcs package installed. If you using the git checkout, you should have already got this installed via composer, but if not do a ```composer update```.

A special set of Known code style rules is included with this package. To test against these rules, run the following from the Known root directory:

```vendor/squizlabs/php_codesniffer/bin/phpcs --standard=vendor/mapkyca/known-phpcs/configuration.xml --extensions=php .```

PHP Code Sniffer works with individual folders, also. For example, to test the core `Idno` engine folder, you can run:

```vendor/squizlabs/php_codesniffer/bin/phpcs --standard=vendor/mapkyca/known-phpcs/configuration.xml --extensions=php Idno```

The codebase is in the process of being brought in line with these rules, and will be integrated with our continuous integration testing once this has been completed.

Contents of the `external` folder are exempt from these rules and will not be tested. 
