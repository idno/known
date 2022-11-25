# Known Generic OAuth2 Client (experimental)

This is a generic, and very experimental, OAuth2 "log in with" plugin. 

This is very early days and was really written to implement an MVP proof of concept thingy for a client of mine. However, it could be more widely useful and with a bit of work could be handy for folk. 

## Installation

* Check it out
* Run ```composer install``` to get the various libraries
* Put it in your IdnoPlugins directory as OAuth2Client
* Activate in your plugins 

Or

From your known install:

```composer require mapkyca/known-oauth-client```

## Usage

Go to the admin page and create your new buttons by filling in the appropriate details.

Out of the box this plugin WON'T fully log you in as whatever, you need to write your own handler plugin to listen to the ```oauth2/authorised``` event hook. 

This hook is passed an array containing the access token and other details for your to use to match up with a user, or create a new one.

If the hook goes unanswered, the plugin will look for ```id``` or ```username``` in the return JSON.

## See

* Author: [Marcus Povey](https://www.marcus-povey.co.uk)
