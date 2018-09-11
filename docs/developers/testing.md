
Known has a number of PHP unit tests that can be run in order to check that things are working correctly, and to protect against any code regression.

## Running from Github

Unit tests will automatically be run when you submit a pull request on GitHub. You should rectify any errors which are reported, as pull requests with errors will not be merged.

## Running on your local machine

To run all the tests, from the Known root directory simply run ```phpunit```.

Some tests require a connection to your local Known instance, so if this is anything other than *http://localhost*, you need to set the ```KNOWN_DOMAIN``` environment variable.

Before running the unit test, set this by running ```export KNOWN_DOMAIN='MY.KNOWN.DOMAIN'```
