Installation instructions
#########################

Before you begin
----------------

Make sure your server satisfies the :doc:`requirements`.

Some of the technologies involved are a little bit new, so you may have to ask for your web host to install them
specially. We want to help you pick a great host that works well with Known, so we'll be creating a list of the ones
that will just work, out of the box.

Upload Known files
------------------

Right now, there isn't a stable installation package for Known, so every installation lives on the cutting edge. For
now, we assume you're okay with that.

You can place the code on your host in two ways:

#. Download the zip from GitHub. Then upload the uncompressed files to the folder root of your web host.
#. Git clone the repository to an appropriate directory (or just straight into the folder root of your web host).

Explaining how to use Git is beyond the scope of this tutorial, but there are plenty of great tutorials on the web.
You'll find the Known git repository URL on `the main Known GitHub page <https://github.com/idno/idno>`_.

Configure Known
---------------

If your MongoDB installation accepts connections from localhost, and you're happy for your Known MongoDB database to be
called Known, you don't need to do anything else in this section.

If you'd like to use an alternative `MongoDB connection string <http://docs.mongodb.org/manual/reference/connection-string/>`_,
save a file called *config.ini* in the root of your Known installation. Lay it out like this::

    [Database configuration]
    dbstring = "Your MongoDB connection string"
    dbname = "Your preferred Known database name"

You can also include a subset of these items, for example to just change the database name.

Load Known
----------

Launch Known in a web browser.

For now, the first user to register will be the site administrator. Later, the installation script will take care of
this for you.

Register and log in.

Administer Known
----------------

Once you've registered and logged in, click "Administration" in the menu bar. This will allow you to set some site
configuration items, including the site name. You will also be able to enable some plugins from this screen. If you're
using Known as a blog or a closed community, you will probably also want to turn open registration off from here.