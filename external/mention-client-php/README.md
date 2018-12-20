Webmention Client
=================

Client library for sending [webmention](http://indiewebcamp.com/webmention) and [pingback](http://indiewebcamp.com/pingback) notifications.

[![Build Status](https://travis-ci.org/indieweb/mention-client-php.png?branch=master)](http://travis-ci.org/indieweb/mention-client-php)
[![Packagist](https://img.shields.io/packagist/v/indieweb/mention-client.svg)](https://packagist.org/packages/indieweb/mention-client)

Usage
-----

### Basic Usage

Given one of your source URLs, this function will find links on the page,
discover the webmention and pingback endpoints for each, and send mentions for any
it discovers.

```php
<?php
$client = new IndieWeb\MentionClient();
$sent = $client->sendMentions($sourceURL);

echo "Sent $sent mentions\n";
?>
```

The library will fetch the source URL, parse it, and look for the first h-entry,
h-card or h-event. It will then attempt to send webmentions (and pingbacks) to
all URLs found in the object, as either properties or inside the "content" HTML.

The library attempts to find the endpoints by doing a HEAD request to the target URL
and looking at the headers, and if none are found, then it makes a GET request
and searches the body of the page for the rel values.

After finding either pingback or webmention endpoints, the request is sent to each.

Alternatively, you can pass in HTML as the second parameter and the library will
look for ALL absolute links in the HTML instead of fetching your post contents and
looking for the microformats object.

```php
<?php
$client = new IndieWeb\MentionClient();
$sent = $client->sendMentions($sourceURL, $sourceHTML);

echo "Sent $sent mentions\n";
?>
```

### Discovering the Webmention endpoint

Given a target URL, this function will return its webmention endpoint
if found, otherwise it will return false.

```php
<?php
$client = new IndieWeb\MentionClient();
$endpoint = $client->discoverWebmentionEndpoint($targetURL);
$endpoint = $client->discoverPingbackEndpoint($targetURL);
?>
```

### Sending a Webmention

To send a webmention to a target URL, you can use the function below. This will
first discover the webmention endpoint of the target, and if found, will then
send the webmention payload to it. You can pass an additional parameter to include
other properties in the payload.

```php
<?php
$client = new IndieWeb\MentionClient();
$response = $client->sendWebmention($sourceURL, $targetURL);
$response = $client->sendWebmention($sourceURL, $targetURL, ['vouch'=>$vouch]);
?>
```

If no webmention endpoint was found at the target, the function will return false.
See the function below for an example of the response when the webmention is successful.

You can also check if the endpoint advertises a webmention endpoint before trying
to send one:

```php
<?php
$client = new IndieWeb\MentionClient();
$supportsWebmention = $client->discoverWebmentionEndpoint($targetURL);
if($supportsWebmention) {
  $client->sendWebmention($sourceURL, $targetURL);
}
?>
```

### Sending a Pingback

```php
<?php
$client = new IndieWeb\MentionClient();
$response = $client->sendPingback($sourceURL, $targetURL);
?>
```

You can also check if the endpoint advertises a pingback endpoint before trying
to send one:

```php
<?php
$client = new IndieWeb\MentionClient();
$supportsPingback = $client->discoverPingbackEndpoint($targetURL);
if($supportsPingback) {
  $client->sendPingback($sourceURL, $targetURL);
}
?>
```

### Sending the Webmention or Pingback directly

To send the actual webmention or pingback payload, you can use the static functions below.
You can pass additional properties for the webmention request in an array if needed.

```php
<?php
$response = IndieWeb\MentionClient::sendWebmentionToEndpoint($endpoint, $source, $target);
$response = IndieWeb\MentionClient::sendWebmentionToEndpoint($endpoint, $source, $target, ['vouch'=>$vouch]);
?>
```

The response is an array containing the HTTP status code, HTTP headers, and the response body:

```json
{
  "code": 202,
  "headers": {
    "Content-Type: text/plain"
  },
  "body": "Webmention is processing"
}
```

You can check if the webmention was accepted by testing if the response code is 200, 201 or 202.

```php
<?php
$success = IndieWeb\MentionClient::sendPingbackToEndpoint($endpoint, $source, $target);
?>
```

The pingback function returns true or false depending on whether the pingback was successfully sent.



### Finding target URLs in a source document

If you have a rendered HTML page (or partial HTML page), you can use this function to
return a list of outgoing links found on the page.

```php
<?php
$client = new IndieWeb\MentionClient();
$urls = $client->findOutgoingLinks($html);
?>
```

Alternately, you can pass a parsed Microformats object to the `findOutgoingLinks`
function and it will search for URLs in any property as well as in the HTML of
any e-content objects.

```php
$client = new IndieWeb\MentionClient();
$parsed = \Mf2\parse($html, $sourceURL);
$urls = $client->findOutgoingLinks($parsed);
```

All links found will be returned an array, with duplicate URLs removed. If no links
are found, it will return an empty array.

```json
[
  "http://example.com/1",
  "http://example.com/2"
]
```

### Custom User Agent

You can set the user agent that this library uses when making HTTP requests.

```php
IndieWeb\MentionClient::setUserAgent('Custom user agent string');
```

At that point, any HTTP request (GET, HEAD, POST) that this library makes will include the user agent header you've set.


### Debugging

If you want to collect debugging information so you can see the steps the library
is doing, run `IndieWeb\MentionClient::enableDebug();` before calling any other function.




About Webmention
----------------

To learn more about Webmention, see [webmention.net](http://webmention.net).

The [webmention.io](http://webmention.io/) service can also act as a pingback->webmention
proxy which will allow you to accept pingbacks as if they were sent as webmentions.


About Pingback
--------------

If you want to accept pingbacks on your site, check out [webmention.io](http://webmention.io/#use-it)
which handles accepting the XMLRPC request and exposes the data via an API.


License
-------

Copyright 2013-2017 by Aaron Parecki and contributors

Available under the Apache 2.0 and MIT licenses.

#### Apache 2.0

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

   http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.

#### MIT

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
