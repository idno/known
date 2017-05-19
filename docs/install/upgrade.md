# Upgrading Known

Upgrading Known is designed to be very simple.

1. Take a backup of your Known directory and database.
2. Overwrite your Known files _except_ your upload directory and your config.ini file.
3. Access your Known site homepage.

That's it!

If you are upgrading from a significantly older version of Known, or if a config.ini file no longer exists in your
installation directory, you may need to perform the following additional steps:

4. Click on the set up button
5. Enter in your database name, user name, host name and password
6. Update the path for the "Uploads" folder. (It may automatically default to the proper path name, but double check it.)
7. Hit enter and you'll be taken to your site.

Thanks to [Chris Aldich](http://stream.boffosocko.com/2015/upgrading-withknown-on-ones-own-server) for this point.

!!! warning "MongoDB Users"
    * Previous releases of Known (<0.9.5) used a now deprecated mongo driver. If you are running your site on Mongo, you will first need to make sure that you have installed the new [PHP MongoDB driver](https://secure.php.net/manual/en/set.mongodb.php).
    * Additionally, the latest version changed the default database engine, so you'll need to specify ```database = 'mongo'``` in your config.ini