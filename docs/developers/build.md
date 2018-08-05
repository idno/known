
Known will run out of the box, and doesn't necessarily need to be "built" first. However, as a developer, you will sometimes need to perform certain steps in order for your changes (e.g. javascript / css) to be reflected.

## Gruntfile

To simplify deployment of JS and CSS, Known makes use of Grunt. 

A Gruntfile is provided which will perform minification of js and css for you, as well as execute other useful tasks.

To get going, install grunt:

```npm install -g grunt-cli```

You will then need to install the following helper tasks:

* ```npm install grunt-contrib-uglify --save-dev```
* ```npm install grunt-contrib-cssmin --save-dev```