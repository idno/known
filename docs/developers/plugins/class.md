# The plugin class

## Name, namespace and inheritance

The main plugin class must have the name Main, and sit inside the IdnoPlugins\PluginName namespace, where PluginName is
the name of your plugin.

It must also inherit the \Idno\Common\Plugin class.

For example, a declaration of the main plugin class for a plugin called Banana might look like::

     namespace IdnoPlugins\Banana {
        class Main extends \Idno\Common\Plugin {
        }
     }

At its very simplest, this is all the code you need for a working plugin. For example, if you just want to replace or
add some templates, this is fine.

## Useful methods

Idno provides a handful of useful methods that you can register in your main plugin class.

### registerPages()

If this exists, this function will be called when the plugin is loaded. It's suggested that you define your
page URLs here.

If you're extending any templates, it's a good idea to include those declarations here too.

For example, the Status plugin contains the following registerPages() function (in fact, this is the *only* method
in the \IdnoPlugins\Status\Main class).

    function registerPages() {
        \Idno\Core\Idno::site()->addPageHandler('/status/edit/?', '\IdnoPlugins\Status\Pages\Edit');
        \Idno\Core\Idno::site()->addPageHandler('/status/edit/([A-Za-z0-9]+)/?', '\IdnoPlugins\Status\Pages\Edit');
        \Idno\Core\Idno::site()->addPageHandler('/status/delete/([A-Za-z0-9]+)/?', '\IdnoPlugins\Status\Pages\Delete');
    }

This is also where plugins should [extend templates](../templating/extending.md). For example, this is how the
`Checkin` plugin adds some mapping JavaScript to the HTML page header:

    \Idno\Core\Idno::site()->template()->extendTemplate('shell/head','checkin/head');

### registerEventHooks()

If this exists, this function will be called when the plugin is loaded. It's suggested that you define your
event hooks here.

