# Extending templates

You may wish to extend templates for a variety of reasons. For example, a plugin may wish to display extra information
after the page footer, or add a new feature to user profiles.

## Plugins

[Plugins](../plugins/index.md) extend themes as part of their [main plugin class](../plugins/class.md),
as part of the `registerPages()` method.

This is how the `Checkin` plugin adds some mapping JavaScript to the HTML page header::

    \Idno\Core\Idno::site()->template()->extendTemplate('shell/head','checkin/head');

## Themes

[Themes](../themes/index.md) have a special way of extending themes as part of their theme.ini file.