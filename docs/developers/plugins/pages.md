# Pages in Known

Plugins will often have to define new pages, or override pages provided by the core platform. Here's how it's done.

## Defining page routes

Your [plugin class](class.md) should define the registerPages() method. This is called as part of the initialization
process when the plugin is loaded.

    function registerPages()
    {
        // Page handling code goes here
    }

There are two main methods to define new page routes. In each case, a regular expression is used to define the URL route
itself, and the name of a class that inherits `\Idno\Common\Page` that will handle the route is supplied.

Note that:

* Page route regular expressions start with a preceding slash ("/"), and should be agnostic about whether they end with
  a slash.
* All page classes must extend `\Idno\Common\Page` and be stored in the `/Pages/` subfolder of your plugin.

### Defining a new page route

You can define a new page route by calling `\Idno\Core\Idno::site()->addPageHandler($route, $class)`. For example, to create
a new page route that handles `http://yoursite.com/testpage/`, your `registerPages()` method might look something like:

    function registerPages()
    {
        \Idno\Core\Idno::site()->addPageHandler('/about/?', '\IdnoPlugins\MyPluginName\Pages\MyPage');
    }

Here, your page class should be stored in the `/Pages/` subfolder of your plugin, with the filename `MyPage.php`.

### Overriding an existing page route

Sometimes you want to override a page route that is provided by the core framework or another plugin. While you can use
`addPageHandler()` here too, it's not guaranteed to take control of the route. Instead, you should use
`\Idno\Core\Idno::site()->hijackPageHandler($route, $class)`.

The syntax is the same:

    function registerPages()
    {
        \Idno\Core\Idno::site()->hijackPageHandler('/existing/?', '\IdnoPlugins\MyPluginName\Pages\MyExistingPage');
    }

### Making page URLs available publicly on non-public sites

By default, pages are hidden behind an authentication wall if the site has been configured to be a walled garden.
However, you may wish to show it anyway, for example to host an "about" page or create a way to apply to join.

In these cases, simply add a third parameter to `addPageHandler()` and `hijackPageHandler()`, set to `true`. For the
examples used above, this would look like:

    function registerPages()
    {
        \Idno\Core\Idno::site()->addPageHandler('/about/?', '\IdnoPlugins\MyPluginName\Pages\MyPage', true);
        \Idno\Core\Idno::site()->hijackPageHandler('/existing/?', '\IdnoPlugins\MyPluginName\Pages\MyExistingPage', true);
    }

## Handling page loads

Once a page route has been defined, an object with the class specified in `addPageHandler()` or `hijackPageHandler()` is
instantiated. This _must_ extend `\Idno\Common\Page`. For example:

    namespace IdnoPlugins\MyPluginName\Pages {
        class MyPage extends \Idno\Common\Page {
            // Page handling content
        }
    }

This file is autoloaded, and _must_ be stored with the filename `Pages/MyPage.php` relative to your plugin's root
folder.

GET and POST requests are handled using methods, as described below.

### GET requests

To handle a GET request, simply override the `getContent()` method. For example, to handle a GET request and display
a simple [template](../templating/index.md), you could use:

    function getContent()
    {
        $t = \Idno\Core\Idno::site()->template();
        $t->body  = $t->draw('template/name/');
        $t->title = 'Title of your page';
        $t->drawPage();
    }

### POST requests

Handling a POST request is very similar to handling GET requests. You just call `postContent()`:

    function postContent()
    {
        $t = \Idno\Core\Idno::site()->template();
        $t->body  = $t->draw('template/name/');
        $t->title = 'Title of your page';
        $t->drawPage();
    }

Note, however, that you can't simply call a POST request in Known. You need to [sign your requests](forms.md)
otherwise Known will reject your content.

Because POST requests [can accept JSON or POST data](forms.md) via an API call or a standard form submission, every
page in Known that accepts POST requests is also an API endpoint.

If your POST request is being submitted via JSON, the response template will automatically be set to JSON, and any
response data will come back to the user as structured JSON.

Note that browser-based POST requests will automatically forward the user to the homepage after execution unless the
user is forwarded elsewhere first.

### Useful methods

So far we've seen how you can display content in response to a GET or POST request. What if you need to accept input,
control access, or otherwise add logical nuance to your pages?

#### Accept input

To take input from the user, use the `$this->getInput($name)` method. This will retrieve GET data in a GET request
and POST or JSON data in a POST request.

For example, to retrieve an ID in a GET request:

    function getContent()
    {
        $id = $this->getInput('id');
    }

#### Forward the page

You can forward the user to a new page using the `$this->forward($url)` method. Execution will be halted and the
browser will be forwarded to a new page.

For example, to forward the user to `/some/arbitrary/page`:

    function getContent()
    {
        $this->forward(\Idno\Core\Idno::site()->getDisplayURL() . 'some/arbitrary/page/');
    }

#### Require authentication

Use `$this->gatekeeper()` to require authentication. If the user isn't logged in, they will be asked to authenticate
before proceeding, and then brought back to the page. For example:

    function getContent()
    {
        $this->gatekeeper();
    }

There are a number of variants on this function:

* `$this->createGatekeeper()` checks to ensure the user is logged in and can create content
* `$this->adminGatekeeper()` checks to ensure the user is logged in and is a site administrator
* `$this->reverseGatekeeper()` checks to ensure the user is logged _out_ and otherwise forwards them to the homepage
* `$this->sslGatekeeper()` checks to ensure the page is being accessed via SSL/TLS

#### Set the HTTP response code

Use `$this->setResponse($code)` to set the HTTP response code. For example, to set a 404:

    function getContent()
    {
        $this->setResponse(404);
    }

#### Get the page URL

Use `$this->currentURL()` to retrieve the full current page URL.

    function getContent()
    {
        $url = $this->currentURL();
    }

#### Deny access or declare content missing

A number of functions exist for particular access control use cases:

* `$this->deniedContent()` displays a standard "access denied" error page and sets HTTP code 403.
* `$this->noContent()` displays a "content not found" error page and sets HTTP code 404.
* `$this->goneContent()` displays a "this content was removed" error page and sets HTTP code 410.

In each case, execution is halted.