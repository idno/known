#Timezones

A jQuery plugin to turn a select box into a timezone selector

##Installation

###Bower

`bower install timezones`

###Manual Download

- [Development]()
- [Production]()

##Usage

Timezones is a simple plugin to populate a `select` element with Current Timezones.

Usage is really simple

```js
$('select').timezones();
```

The plugin will try as well to guess your current timezone from [`Date.getTimezoneOffset`](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Date/getTimezoneOffset?redirectlocale=en-US&redirectslug=JavaScript%2FReference%2FGlobal_Objects%2FDate%2FgetTimezoneOffset)

##Development

###Requirements

- node and npm
- bower `npm install -g bower`
- grunt `npm install -g grunt-cli`

###Setup

- `npm install`
- `bower install`

###Run

`grunt dev`

or for just running tests on file changes:

`grunt ci`

###Tests

`grunt mocha`
