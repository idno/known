[![Build Status](https://travis-ci.org/idno/idno.svg?branch=master)](https://travis-ci.org/idno/idno)

# Idno: a social group platform

![Idno - A social group platform](https://idno.co/img/home/screens.png)

## Installation 

### One-click Idno sites

If you want to install on your own web space, we recommend [Reclaim Hosting](https://portal.reclaimhosting.com/aff.php?aff=013),
which includes one-click Idno installation. Idno is also known to work on [DreamHost](https://dreamhost.com), a high-quality
web hosting provider.

### Installing

Idno is under active development and requires PHP 8.1+ with selected extensions, together with a supported database backend. You can find detailed installation instructions here: <http://docs.idno.co/en/latest/install/index.html>

#### Installing from packages

_Unofficial_ install packages, which are periodically built from the latest code, are available: <https://www.marcus-povey.co.uk/known/>

#### Installing from Github

You can opt to check out the work-in-progress development code from the git repository: <https://github.com/idno/idno>

* Check out the repo: ```git clone https://github.com/idno/idno.git```
* Fetch dependencies: ```cd idno; composer install```

#### Installing with composer

You can install Idno directly from composer using: ``` composer create-project idno/idno ```

Optionally, you can install the latest bleeding edge code the same way: ``` composer create-project idno/idno -s dev ```

### Setting up the async pipeline

By default, Idno processes events like Webmention pings and syndication to external services synchronously during page requests. You can enable asynchronous event processing to improve page load times by deferring these operations to a background worker.

#### 1. Enable the async queue

Add the following line to your `config.ini`:

```ini
event_queue = 'AsynchronousQueue'
```

#### 2. Run the event queue worker

Start the dispatch service using the Idno console tool. Run it as your web server user so it can read and write files:

```bash
sudo -u www-data KNOWN_DOMAIN='your.domain' ./known service-event-queue
```

This process must stay running to dispatch queued events. Use a process manager (e.g., systemd, supervisord) to keep it alive.

#### 3. Run the periodic cron service (optional)

If you need periodic background tasks (triggered via `cron/minute`, `cron/hourly`, and `cron/daily` events), start the cron service:

```bash
sudo -u www-data KNOWN_DOMAIN='your.domain' ./known.php service-cron
```

**Important:** When you update Idno core or any plugins, restart both `service-event-queue` and `service-cron` so they run the updated code.

For more details, see the [advanced configuration docs](docs/install/advanced.md).

### Support us

* [Star us on GitHub](https://github.com/idno/idno)
* [Like us on alternativeto.net](http://alternativeto.net/software/known/)
* [Contribute](CONTRIBUTING.md)

### Get support

* Try the open source mailing list: <https://groups.google.com/forum/#!forum/known-dev>

## Community links

* Learn more and sign up to get updates: <https://idno.co>
* Full project documentation: <http://docs.idno.co/>
* Join the development mailing list: <https://groups.google.com/forum/#!forum/known-dev>
* Join the IRC channel: [#knownchat](https://webchat.freenode.net/?channels=knownchat) on Freenode

For details on contributing to the Idno project, please read [CONTRIBUTING.md](CONTRIBUTING.md).

## Contributors

This project exists thanks to all the people who contribute. [[Contribute](CONTRIBUTING.md)].

See [contributors on GitHub](https://github.com/idno/idno/graphs/contributors).

## Copyright and License

Except for included third-party projects, Idno is (c) Idno, Inc.

Unless otherwise stated, Idno is licensed under the Apache Software License 2.0. See [LICENSE](LICENSE) for more information.

Idno logos are (c) Idno, Inc. Permission from Idno, Inc is required to use the Idno name or logo as part of any
project, product, service, domain or company name, except as included in official themes distributed by Idno.

Logos of external services are (c) their respective owners. All rights reserved.

Third party libraries are licensed separately.

### Idno also contains

* Twitter Bootstrap, which is distributed under the Apache 2.0 license. Source: https://github.com/twitter/bootstrap
* jQuery, which is distributed under the MIT License. Source: https://github.com/jquery/jquery
* Portions of Symfony, which is distributed under the MIT license.
  * EventDispatcher. Source: https://github.com/symfony/EventDispatcher
  * HttpFoundation. Source: https://github.com/symfony/HttpFoundation
  * Console. Source: https://github.com/symfony/console
* ToroPHP, which is distributed under the MIT License. Source: https://github.com/anandkunal/ToroPHP/
* Fork Awesome, which is distributed under the Open Font License, version 1.1: https://github.com/ForkAwesome/Fork-Awesome
* Steve Clay's AutoP, which is distributed under the MIT License. Source: https://code.google.com/p/mrclay/
* Aaron Parecki's Webmention Client, which is distributed under the Apache 2.0 license. Source: https://github.com/aaronpk/mention-client
* Barnaby Walters's Microformats 2 Parser, which is distributed under the MIT License. Source: https://github.com/indieweb/php-mf2
* FitVids.js, which is distributed under the WTFPL License. Source: http://fitvidsjs.com/
* Leaflet.js, which is distributed under the BSD 2-Clause License. Source: http://leafletjs.com/ 
* SwiftMailer, which is distributed under the MIT License. Source: https://github.com/swiftmailer/swiftmailer
* Antwort, an email template which is distributed under the MIT License. Source: https://github.com/internations/antwort
* Mention.js, a Bootstrap user at-mention library, which is distributed under the MIT License. Source: https://github.com/jakiestfu/Mention.js
* MediaElement.js, a cross-browser media player, which is distributed under the MIT License. Source: https://github.com/johndyer/mediaelement
* Simplepie, a feed parser, which is distributed under the BSD 3-Clause License. Source: https://github.com/simplepie/simplepie/
* Bootstrap Toggle, which is distributed under the MIT License. Source: http://www.bootstraptoggle.com/
* TinyMCE, a rich text editor, which is distributed under the LGPL License. Source: https://github.com/tinymce/tinymce
* The Paypal Bootstrap Accessibility Plugin, which is distributed under the BSD 3-Clause License. Source: https://github.com/paypal/bootstrap-accessibility-plugin
* HTMLPurifier, which is distributed under the LGPL License. Source: http://htmlpurifier.org
* Wavesurfer, which is distrubuted under a Creative Commons Attribution 3.0 Unported License. Source: https://github.com/katspaugh/wavesurfer.js
* MongoDB-PHP-Library, which is distributed under the Apache 2 Licence. Source: https://github.com/mongodb/mongo-php-library
* Exif-js, which is distributed under the MIT Licence. Source: https://github.com/exif-js/exif-js
* PHP-OGP, distributed under the GPL2 Licence. Source: https://github.com/mapkyca/php-ogp

## Thank you

[<img src="https://user-images.githubusercontent.com/624104/52508260-d0daa180-2ba8-11e9-970c-3ef9596f6b4e.png" alt="BrowserStack Logo" width="120">](https://www.browserstack.com/)

Thanks to [BrowserStack](https://www.browserstack.com/) for providing the infrastructure that allows us to test in real browsers.
