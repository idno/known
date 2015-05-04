# Changes by version

Starting with version 0.6.4, this is a list of changes in the self-hosted, open source version of Known.

0.7.8
-----
May 4, 2015

* Hashtags now handle unicode character sets
* Improved PubSubHubbub handling
* Installation now detects rewrite rule support more reliably
* Sessions do not persist between http and https
* Site URL in public comments is now optional
* Better mobile Twitter URL support
* Internal web client is more configurable
* Object annotations are included in JSON
* Bookmarks syndicate to Twitter
* Included tweets auto-embed in status updates
* Includes Vagrant and Ansible configuration files

0.7.7.1
-------
April 10, 2015

* Corrected brid.gy connection flow

0.7.7
-----
April 9, 2015

* More consistent publishing flow
* Introducing Convoy for easier social media connections
* Faster database access for some tasks
* Introducing getStaticURL for static resources (eg for use with CDNs)

0.7.6
-----
March 31, 2015

* Better bookmark page title handling (again!)
* Improved session handling
* Thumbnails are higher quality
* Further improvement to Open Graph tag handling
* Improvement to PubSubHubbub HTTP headers
* A number of fixes across plugins
* Experimental release of WordPress importer

0.7.5
-----
March 2, 2015

* Better bookmark page title handling
* Improved username matching
* Improved email handling
* Exports better SQL
* Import from external blogging platforms like Blogger
* Source code highlighting
* Simpler link to brid.gy
* More efficient user session handling
* Better syndication workflow
* Improved open graph tag handling

0.7.1
-----
February 8, 2015

* Improved support for root-level domains
* Fixed bug with HTML pasted from other sources

0.7
---
January 31, 2015

* Infinite accounts per syndication service
* Static pages
* Webhooks
* Improved interface when AdBlock Plus is used
* Changed rich text editor to TinyMCE
* Introducing the Uploads folder, for easier installation / configuration
* Introducing prerequisite plugins, which are always loaded first
* Webmention client now uses internal web services API
* Improved export format
* Improved session user storage
* Numerous internal API and interface improvements

0.6.5
-----
November 24, 2014

* API improvements
* Cleaner hashtag and username parsing
* PubSubHubbub implemented by default
* KML output template
* Delete users from the user admin panel
* Better button behavior when saving content
* Authentication can be overridden and extended by plugins
* Framework for syndicating to multiple accounts on the same service
* Installer is more compatible with shared hosts like Arvixe (and easier overall)
* More compatible with sites that switch to using HTTPS
* Better compatibility with brid.gy
* Better compatibility with Amazon Elastic Beanstalk
* .htaccess is more compatible with a wider range of hosts

0.6.4
-----
October 27, 2014

* Export your data
* Improved session handling, particularly for MongoDB installations
* .htaccess is now more compatible with shared hosts like Dreamhost and GoDaddy
* Fewer system requirements for installation
* No need for 'http://' in profile URLs
* Improved Micropub support
* More efficient file interface
* Some style improvements, particularly with input fields
* Bookmarklet now opens in a new page
* Removed humans.txt
* More traditional array and other internal syntax
* Additional stability and speed improvements

0.6.3
-----
September 23, 2014