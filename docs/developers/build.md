# Building Known

Known will run out of the box, and doesn't necessarily need to be "built" first. However, as a developer, you will sometimes need to perform certain steps in order for your changes (e.g. javascript / css) to be reflected.

## Building javascript

To simplify deployment of JS and CSS, Known makes use of Grunt. 

A Gruntfile is provided which will perform minification of js and css for you, as well as execute other useful tasks.

To get going, install grunt:

```npm install -g grunt-cli```

You will then need to install the following helper tasks:

* ```npm install grunt-contrib-uglify --save-dev```
* ```npm install grunt-contrib-cssmin --save-dev```
* ```npm install grunt-contrib-jshint --save-dev```
* ```npm install grunt-contrib-watch --save-dev```
* ```npm install grunt-contrib-csslint --save-dev```

### Testing your changes

If you've made JS changes, you can check your code using ```grunt test``` to run linting, etc

### Minify your changes

To get your javascript or css changes to be used by Known, you'll need to build minified versions of them. Do this by running the default grunt task by typing ```grunt```

!!! note "Vagrant"
    As a developer, you may find it helpful to set up a virtual machine to see your code running. A Vagrant configuration for Known can be found on [Github](https://github.com/mapkyca/known-vagrant).
