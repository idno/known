{\rtf1\ansi\ansicpg1252\cocoartf1265\cocoasubrtf210
{\fonttbl\f0\fswiss\fcharset0 Helvetica;}
{\colortbl;\red255\green255\blue255;}
\margl1440\margr1440\vieww10800\viewh8400\viewkind0
\pard\tx720\tx1440\tx2160\tx2880\tx3600\tx4320\tx5040\tx5760\tx6480\tx7200\tx7920\tx8640\pardirnatural

\f0\fs24 \cf0 # Installation instructions\
\
##Before you begin\
\
If you\'92re running Known in production, we highly recommend that you download the installation package from [withknown.com](https://withknown.com).\
\
To begin with, make sure your server satisfies the _System requirements_.\
\
Some of the technologies involved are a little bit new, so you may have to ask for your web host to install them specially. We want to help you pick a great host that works well with Known, so we'll be creating a list of the ones that will just work, out of the box.\
\
##Upload Known files\
\
Known releases stable installation packages from [withknown.com](https://withknown.com) in both .zip and .tar.gz formats. If you are using Known for any purpose other than development, this is the recommended source for Known installations.\
\
Note that Known currently does not support installation in subdirectories. Your Known site must be at the root of your domain or subdomain.\
\
You can place the platform on your web host by:\
\
+ Downloading the latest package from [the Known homepage](https://withknown.com/). This is by far the easiest option. If you\'92ve uploaded the files inside the archive to your web host, you can skip to the configuration section of these documents, below.\
+ Git clone the repository to an appropriate directory (or just straight into the folder root of your web host). Note that you need to make sure you acquire [the Git submodules](http://git-scm.com/book/en/v2/Git-Tools-Submodules). eg: ```git clone --recursive git@github.com:idno/idno.git /path/to/webroot```\
+ If you git cloned the repository to your local disk, use a file transfer app to move the files to your web host.\
\
Explaining how to use Git is beyond the scope of this tutorial, but there are plenty of great tutorials on the web. Make sure to enable the [Git submodules](http://git-scm.com/book/en/v2/Git-Tools-Submodules). You\'92ll find the Known git repository URL on [the main Known GitHub page](https://github.com/idno/idno).\
\
## Configure Known\
\
### Use the automatic installer\
\
If you\'92re using a MySQL back-end, you can get started by pointing your browser at your Known site address. If you want to use MongoDB, you\'92ll need to create the configuration file manually, as described below.\
\
### Use environment variables\
\
If you\'92re using Docker or other virtualized server environments, you will need to create a config.ini file at the root of your installation. However, all configuration items can be stored in environment variables starting with KNOWN_. For example, the following environment variables will allow you to set the database:\
\
    KNOWN_DATABASE = "MySQL"\
    KNOWN_DBNAME = "KnownDBName"\
    KNOWN_DBUSER = "KnownDBUser"\
    KNOWN_DBPASS = "KnownDBPassword"\
    KNOWN_DBHOST = "your.database.server"\
	\
### If you\'92re using MongoDB\
\
If your MongoDB installation accepts connections from localhost, and you\'92re happy for your Known MongoDB database to be called Known, you can simply create a file called ```config.ini``` in the root of your installation containing:\
\
    database = "MongoDB"\
\
If you\'92d like to use an alternative [MongoDB connection string](http://docs.mongodb.org/manual/reference/connection-string/), you can add that to ```config.ini``` like this:\
\
    [Database configuration]\
    dbstring = "Your MongoDB connection string"\
    dbname = "Your preferred Known database name"\
	\
You can also include a subset of these items, for example to just change the database name.\
\
### If you\'92re using MySQL\
\
Currently, MySQL users need to create a file called ```config.ini``` in the root of their installation. This should contain the following information:\
\
    database = "MySQL"\
    dbname = "Your MySQL database name"\
    dbhost = "Your MySQL database host (eg 'localhost')"\
    dbuser = "Your database username"\
    dbpass = "Your database password"\
	\
If you need to use a non-standard database port, you can also select this:\
\
    dbport = "Your database port"\
\
Additionally, you will need to create the database referred to in this configuration file, and ensure that it can be connected to using the user credentials you supply. For now, you will need to load the SQL schema stored in /schemas/mysql/mysql.sql.\
\
### Set the filesystem\
\
If you\'92re using MongoDB, you don\'92t have to do anything, and all uploaded files will be stored in MongoDB itself. However, you can also store files on your server\'92s local hard drive. Additionally, plugins can provide alternative filesystems like Amazon S3.\
\
#### Store files on your server\'92s hard drive\
\
Create a directory where file uploads will be stored. This must be outside of your document root. Set permissions such that the web server can read and write to it. chmod 777 will work, but is insecure and not recommended.\
\
Make a note of that full path. For example, /Users/ben/Sites/withknown.com/data/.\
\
Then, add the following to your config.ini file:\
\
    filesystem = "local"\
    uploadpath = "/Users/ben/Sites/withknown.com/data/"\
	\
Of course, replace the path with the path to your data folder.\
\
### Load Known\
\
Launch Known in a web browser.\
\
For now, the first user to register will be the site administrator. Later, the installation script will take care of this for you.\
\
Register and log in.\
\
### Administer Known\
\
Once you\'92ve registered and logged in, click \'93Administration\'94 in the menu bar. This will allow you to set some site configuration items, including the site name. You will also be able to enable some plugins from this screen. If you\'92re using Known as a blog or a closed community, you will probably also want to turn open registration off from here.\
\
\
\
}