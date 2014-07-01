Extending templates
===================

You may wish to extend templates for a variety of reasons. For example, a plugin may wish to display extra information
after the page footer, or add a new feature to user profiles.

Plugins
-------

:doc:`Plugins <../plugins/index>` extend themes as part of their :doc:`main plugin class <../plugins/class>`,
in the `registerPages()` plugin.

This is how the `Checkin` plugin adds some mapping JavaScript to the HTML page header::

    \Idno\Core\site()->template()->extendTemplate('shell/head','checkin/head');

Themes
------

:doc:`Themes <../themes/index>` have a special :ref:`way of extending themes as part of their theme.ini file <themes_extending_templates>`.