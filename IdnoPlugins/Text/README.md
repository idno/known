# Idno/Text

A Known plugin that allows post status updates with a light-weight _blogging engine_.

## Installation

* Drop the `Text` folder into the `IdnoPlugins` folder of your Known installation.
* Log into Known and click on Administration.
* Click "enable" next to the Text plugin.

## Development

* Checkout repo in the `IdnoPlugins` directory.
* Make your changes and test them.
* Update languages file `npm run grunt build-lang`
* Update the version number in `composer.json`
* Beautify your php `

```
vendor/squizlabs/php_codesniffer/bin/phpcbf --standard=vendor/mapkyca/known-phpcs/configuration.xml --extensions=php .
```

## License

Released under the Apache 2.0 license: http://www.apache.org/licenses/LICENSE-2.0.html
