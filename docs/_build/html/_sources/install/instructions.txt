Installation instructions
#########################

Before you begin
----------------

By installing Known now, you're working on the cutting edge. When we release this summer, there will be a friendly
installer, as well as a full hosted service for you to check out the features. Right now, there's a little bit more
involved.

To begin with, make sure your server satisfies the :doc:`requirements`.

Some of the technologies involved are a little bit new, so you may have to ask for your web host to install them
specially. We want to help you pick a great host that works well with Known, so we'll be creating a list of the ones
that will just work, out of the box.

Upload Known files
------------------

Right now, there isn't a stable installation package for Known, so every installation lives on the cutting edge. For
now, we assume you're okay with that. A friendly installer with everything you need to get going will be released
this summer.

You can place the code on your host by:

* Git clone the repository to an appropriate directory (or just straight into the folder root of your web host).
  Note that you need to make sure you acquire `the Git submodules <http://git-scm.com/book/en/Git-Tools-Submodules>`_.
  eg: ```git clone --recursive git@github.com:idno/idno.git /path/to/webroot```
* If you git cloned the repository to your local disk, use a file transfer app to move the files to your web host.

Explaining how to use Git is beyond the scope of this tutorial, but there are plenty of great tutorials on the web. 
Make sure to enable the `Git submodules <http://git-scm.com/book/en/Git-Tools-Submodules>`.
You'll find the Known git repository URL on `the main Known GitHub page <https://github.com/idno/idno>`_.

Configure Known
---------------

If you're using MongoDB
^^^^^^^^^^^^^^^^^^^^^^^

If your MongoDB installation accepts connections from localhost, and you're happy for your Known MongoDB database to be
called Known, you don't need to do anything else in this section.

If you'd like to use an alternative `MongoDB connection string <http://docs.mongodb.org/manual/reference/connection-string/>`_,
save a file called *config.ini* in the root of your Known installation. Lay it out like this::

    [Database configuration]
    dbstring = "Your MongoDB connection string"
    dbname = "Your preferred Known database name"

You can also include a subset of these items, for example to just change the database name.

If you're using MySQL
^^^^^^^^^^^^^^^^^^^^^

Currently, MySQL users need to create a file called :doc:`config.ini` in the root of their installation. This should
contain the following information::

        database = "MySQL"
        dbname = "Your MySQL database name"
        dbhost = "Your MySQL database host (eg 'localhost')"
        dbuser = "Your database username"
        dbpass = "Your database password"

Additionally, you will need to create the database referred to in this configuration file, and ensure that it can be
connected to using the user credentials you supply. For now, you will need to load the SQL schema stored in
/schemas/mysql/mysql.sql.

Set the filesystem
------------------

If you're using MongoDB, you don't have to do anything, and all uploaded files will be stored in MongoDB itself.
However, you can also store files on your server's local hard drive. Additionally, plugins can provide alternative
filesystems like Amazon S3.

Store files on your server's hard drive
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Create a directory where file uploads will be stored. This *must* be outside of your document root. Set permissions
such that the web server can read and write to it. chmod 777 will work, but is insecure and not recommended.

Make a note of that full path. For example, /Users/ben/Sites/withknown.com/data/.

Then, add the following to your config.ini file::

        filesystem = "local"
        uploadpath = "/Users/ben/Sites/withknown.com/data/"

Of course, replace the path with the path to your data folder.

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
