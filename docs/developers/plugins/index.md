# Getting started writing Known plugins

Plugins allow you to extend Known's functionality.

In fact, it's recommended that every piece of functionality or design that you add to Known is done through a plugin,
so  that you can upgrade the core Known platform without jeopardizing your customizations. We've made this extremely
simple.

## Layout

The plugin's folder must sit inside /IdnoPlugins.

Every plugin must have at least two files:

* Main.php: the main plugin class
* plugin.ini: a configuration file that provides information about the plugin in the admin panel

Plugins may also contain the following files:

* ContentType.php: a class that describes a new type of content that your plugin will create (see Content Types)
* Entity.php: each object created with your content type will be an entity of this class (with the name of your choice - it's recommended that you don't actually use Entity)
* A set of pages under the plugin's Pages subfolder
* A set of templates under the plugin's templates subfolder
* Any other static assets or external libraries you wish to include

## Structure of plugin.ini

The plugin.ini file contains a number of text entries:

    [Plugin description]
    name =              "The name of the plugin"
    version =           "The plugin's version number"
    url =               "URL to the repository where this plugin can be downloaded"
    author =            "Author's name"
    author_email =      "Author's email address"
    author_url =        "http://authors-url/"
    description =       "A short description that will be displayed in the administration panel"

More may be added over time. These entries are displayed in Known's administration panel but may also be displayed in
a future plugin directory.

Plugin authors can include a `[requirements]` section with
dependencies on PHP extensions and on other Known plugins (optionally
specifying a minimum version of those plugins):

    [requirements]
    extension[] =       "php extension"
    extension[] =       "second php extension"
    plugin[] =          "Known plugin"
    plugin[] =          "Another Known plugin,0.8"

The requirements section may also define a minimum PHP version and
Known core version.

    php =               5.5
    known =              0.9

This plugin requires PHP >= 5.5 or higher and Known >= 0.9.

At this time, these requirements are informational only: You can still
install a plugin whose requirements are not met, but there will be a
little red notification on the plugins screen.

## Namespaces

Plugins are all defined as classes inside the IdnoPlugins namespace. For example, if your plugin was called Banana,
its folder would be /IdnoPlugins/Banana, and all its classes would be in the IdnoPlugins/Banana namespace. This
namespace maps exactly to the plugin's file location - so, for example, IdnoPlugins/Banana/Main would be found at
/IdnoPlugins/Banana/Main.php. Please do not write a banana plugin. (Or do. We're not the boss of you. Whatever has
appeal.)

If the plugin is not installed, only plugin.ini is ever read by Known, and then only on the plugin administration page.

If the plugin is installed, the plugin is loaded before any output is written to the screen, as follows:

* IdnoPlugins\PluginName\Main is instantiated and saved to memory

 * Any URLs associated with the plugin are registered
 * Any template extensions associated with the plugin are registered
 * Any event hooks associated with the plugin are registered (eg, the plugin might register a function to be called
   when note objects are created)

* IdnoPlugins\PluginName\ContentType (if it exists) is instantiated and saved to memory

## Templates

Each plugin contains its own template directory, which overrides [Known templates](../templating/index.md) from the
core system. For example, you could create a whole new page shell by saving a template in `/IdnoPlugins/Banana/templates/default/page/shell.tpl.php`.

This is a powerful system that, together with the other methods available to plugins, allows you to completely change
the look and feel, and overall functionality, of your Known site.
