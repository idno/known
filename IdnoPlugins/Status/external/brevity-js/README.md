# brevity-js

[![Build Status](https://travis-ci.org/kylewm/brevity-js.svg?branch=master)](https://travis-ci.org/kylewm/brevity-js)

A small utility to count characters, autolink, and shorten posts to an
acceptable tweet-length summary.

This is a port of the Python module of the same name. Please refer to
https://github.com/kylewm/brevity for documentation.

## Installation

With npm, simply `npm install brevity`.

Otherwise, brevity.js is a single file that can be included
anywhere. In the browser, it will define `window.brevity` with the
functions below.  `

## Usage

### tweetLength(text)

Find out how many characters a message will use on Twitter with
`tweetLength()`:

```javascript
var brevity = require("brevity");
var length = brevity.tweetLength('Published my first npm www.npmjs.com/package/brevity and composer packagist.org/packages/kylewm/brevity packages today!');
console.log(length);  // 99
```

This text is 119 characters but, due to t.co wrapping, will only use
99 characters.

### autolink(text)

Convert URLs in plaintext to HTML links.

```javascript
var brevity = require("brevity");
var html = brevity.autolink("I'm a big fan of https://en.wikipedia.org/wiki/Firefly_(TV_series) (and its creator https://en.wikipedia.org/wiki/Joss_Whedon)");
console.log(html);
```

Note that brevity handles parentheses and other punctuation as you'd
expect.

### shorten(text)

The `shorten(text)` function takes a message of any length and
shortens it to a Tweet-length 140 characters, adding an ellipsis at
the end of it is truncated. It will not truncate a word or URL in the
middle. Shorten takes a few *optional* parameters that change the way
the tweet is formed. Any of these parameters can be `null`.

- `permalink` - included after the ellipsis if and only if the text
  is shortened. Must be a URL or false.
- `shortpermalink` - included in parentheses at the end of tweets
  that are not shortened. Must be a URL or false.
- `shortpermacitation` - included in parentheses at the end of tweets
  that are not shortened. Must *not* be a URL, e.g. `ttk.me t4fT2`
- `targetLength` - The target length for the final text. Defaults to
  140.
- `linkLength` - The final length of each URL after
  shortening. Defaults to 23.
- `formatAsTitle` - take the text as a title of a longer
  article. Always formats as "Title: $permalink" or "Title…
  $permalink" if shortened.

```javascript
var brevity = require("brevity");
var permalink = "https://kylewm.com/2016/01/brevity-shortens-notes";
var longnote = "Brevity (github.com/kylewm/brevity-php) shortens notes that are too long to fit in a single tweet. It can also count characters to help you make sure your note won't need to be shortened!";
var tweet = brevity.shorten(longnote, permalink);
console.log(tweet);
```
