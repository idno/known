Installation instructions
#########################

Before you begin
----------------

If you're running Known in production, we highly recommend that you download the installation package from
`withknown.com <https://withknown.com/>`_.

To begin with, make sure your server satisfies the :doc:`requirements`.

Some of the technologies involved are a little bit new, so you may have to ask for your web host to install them
specially. We want to help you pick a great host that works well with Known, so we'll be creating a list of the ones
that will just work, out of the box.

Upload Known files
------------------

Known releases stable installation packages from `withknown.com <https://withknown.com/>`_ in both .zip and .tar.gz
formats. If you are using Known for any purpose other than development, this is the recommended source for Known
installations.

Note that Known currently does not support installation in subdirectories. Your Known site must be at the root of your
domain or subdomain.

You can place the platform on your web host by:

* Downloading the latest package from `the Known homepage <https://withknown.com/>`_. This is by far the easiest
  option. If you've uploaded the files inside the archive to your web host, you can skip to the configuration section
  of these documents, below.
* Git clone the repository to an appropriate directory (or just straight into the folder root of your web host).
  Note that you need to make sure you acquire `the Git submodules <http://git-scm.com/book/en/Git-Tools-Submodules>`_.
  eg: ```git clone --recursive git@github.com:idno/idno.git /path/to/webroot```
* If you git cloned the repository to your local disk, use a file transfer app to move the files to your web host.

Explaining how to use Git is beyond the scope of this tutorial, but there are plenty of great tutorials on the web. 
Make sure to enable the `Git submodules <http://git-scm.com/book/en/Git-Tools-Submodules>`_.
You'll find the Known git repository URL on `the main Known GitHub page <https://github.com/idno/idno>`_.

Configure Known
---------------

Use the automatic installer
^^^^^^^^^^^^^^^^^^^^^^^^^^^

If you're using a MySQL back-end, you can get started by pointing your browser at your Known site address. If you want
to use MongoDB, you'll need to create the configuration file manually, as described below.

Use environment variables
^^^^^^^^^^^^^^^^^^^^^^^^^

If you're using Docker or other virtualized server environments, you will need to create a config.ini file at the root
of your installation. However, all configuration items can be stored in environment variables starting with KNOWN_.
For example, the following environment variables will allow you to set the database::

    KNOWN_DATABASE = "MySQL"
    KNOWN_DBNAME = "KnownDBName"
    KNOWN_DBUSER = "KnownDBUser"
    KNOWN_DBPASS = "KnownDBPassword"
    KNOWN_DBHOST = "your.database.server"

If you're using MongoDB
^^^^^^^^^^^^^^^^^^^^^^^

If your MongoDB installation accepts connections from localhost, and you're happy for your Known MongoDB database to be
called Known, you can simply create a file called :doc:`config.ini` in the root of your installation containing::

    database = "MongoDB"

If you'd like to use an alternative `MongoDB connection string <http://docs.mongodb.org/manual/reference/connection-string/>`_,
you can add that to :doc:`config.ini` like this::

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

If you need to use a non-standard database port, you can also select this::

        dbport = "Your database port"

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

Copy .htaccess
^^^^^^^^^^^^^^

If you are using Known 0.6.5 and Apache 2.4, copy htaccess-2.4.dist to .htaccess; otherwise, copy htaccess.dist to .htaccess.
If you downloaded from git, you may skip this step.

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
