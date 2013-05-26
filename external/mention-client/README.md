Webmention Client
=================

Client library for sending [webmention](http://webmention.org) and [pingback](http://indiewebcamp.com/pingback) notifications.

Usage
-----

```php
<?php
$client = new MentionClient($url);
$client->debug = true;
$sent = $client->sendSupportedMentions();

echo "Sent $sent mentions\n";
?>
```

This will find all absolute links on the page at `$url` and will attempt to send
mentions to each. This is accomplished by doing a HEAD request and looking at the headers
for supported servers, if none are found, then it searches the body of the page.

After finding either pingback or webmention endpoints, the request is sent to each.


Pingback
--------

If you want to accept pingbacks on your site, check out [pingback.me](http://pingback.me)
which handles accepting the XMLRPC request and exposes the data via an API.


Webmention
----------

To learn more about Webmention, see [webmention.org](http://webmention.org).

The [pingback.me](http://pingback.me) project can also act as a pingback->webmention
proxy which will allow you to accept pingbacks as if they were sent as JSON webmentions.


License
-------

Copyright 2013 by Aaron Parecki

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

   http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
