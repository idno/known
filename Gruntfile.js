/**
 * Gruntfile for Known project.
 * 
 * This is a grunt task for building various aspects of Known. You'll only need to 
 * use this if you're a developer working on the core.
 * 
 * Installation
 * 
 *  - npm i 
 * 
 * Useful tasks:
 * 
 *  - grunt - builds everything
 *  - grunt build-lang - recompiles gettext corpus (make sure you composer install before hand to get the build scripts!)
 *  - grunt build-css - Compile the SASS
 *  - grunt build-js - minify javascript and pass it through babel
 *  - grunt test - lint your js and css
 *  - grunt watch - look for changes and recompile as needed (useful for development)
 */

/*jshint ignore:start*/
const sass = require('dart-sass');
/*jshint ignore:end*/

module.exports = function (grunt) {
  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    sass: {
	options: {
	    sourcemap: 'none',
	    implementation: sass,
	    noCache: true
	},
	dev: {
	    files: {
	        'css/known.css': 'css/scss/known.scss',
		'css/known-simple.css': 'css/scss/known-simple.scss'
	    },
	},
	dist: {
	    files: {
	        'css/known.min.css': 'css/scss/known.scss',
		'css/known-simple.min.css': 'css/scss/known-simple.scss'
	    },
	    options: {
	      outputStyle: 'compressed'
	    }
	}
    },
    concat: {
      options: {
      },
      js: {
        files: {
          'js/known.es6': [
	      'js/src/classes/Security.js',
	      'js/src/classes/Logger.js',
	      'js/src/classes/Notifications.js',
	      'js/src/lib/Known.js',
	      'js/src/classes/Unfurl.js',
	      'js/src/classes/Image.js',
	      'js/src/lib/Image.js',
	      'js/src/classes/Template.js',
	      'js/src/lib/Template.js',
	  ],
          'js/service-worker.es6': [
	      'js/src/ServiceWorker.js'
	  ]
        }
      }
    },
    babel: {
      options: {
	sourceType: "module",
        sourceMaps: true,
        "presets": [
          [
            "@babel/preset-env",
            {
              "loose": true,
              "modules": false,
              "targets": {
                esmodules: true
              }
            },
          ]
        ]
      },
      dist: {
	files: [
	  {
	    expand: true,
	    cwd: 'js/',
	    src: ['*.es6'],
	    dest: 'js/',
	    ext: '.js',
	    extDot: 'last'
	  }]
      },
    },
    terser: {
	options: {
          sourceMap: true,
          ecma: 2017,
	},
        sourceMap: {
          url: "file.js.map"
        },
	dist: {
	    files: [{
		expand: true,
		cwd: "js/",
		src: ["*.js", "!*.min.js"],
		dest: "js",
		ext: ".min.js",
		extDot: 'last'
	    }]
	},
    },
    modernizr: {
      dist: {
        "crawl": false,
        "minify": true,
        "uglify": true,
        "options": [
          "setClasses"
        ],
        "tests": [
          "inputtypes"
        ],
        "dest": "js/modernizr/modernizr-custom.js"
      }
    },
    stylelint: {
      all: ['css/scss/*.scss', 'css/scss/known/*.scss']
    },
    jshint: {
      // define the files to lint
      files: [
            'Gruntfile.js',
            'js/src/**/*.js'
      ],

      // configure JSHint (documented at http://www.jshint.com/docs/)
      options: {
        // more options here if you want to override JSHint defaults
        globals: {
          jQuery: true,
          console: true,
          module: true,
          document: true,
          "$": false,
          known: false,
          wwwroot: true,
          base64ToArrayBuffer: true,

          EXIF: true,
          self: true,

          Security: true,
          Template: true,
          ImageTools: true,
        },
        node: true,
        browser: true,
	esversion: 6
      }
    },
    watch: {
        options: {
          dateFormat: function(time) {
            grunt.log.writeln('The watch finished in ' + time + 'ms at ' + (new Date()).toString());
            grunt.log.writeln('Waiting for more changes...');
          },
        },
        sass: {
            files: 'css/scss/**/*.scss',
            tasks:  ['build-css', 'stylelint']
        },
        js: {
            files: ['js/src/**/*.js', 'Gruntfile.js'],
            tasks:  ['build-js', 'jshint']
        } 
    }

  });

  // Load the plugins
  grunt.loadNpmTasks('grunt-sass');
  grunt.loadNpmTasks('grunt-babel');
  grunt.loadNpmTasks('grunt-terser');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-stylelint');
  grunt.loadNpmTasks("grunt-modernizr");


  // Tests
  grunt.registerTask('test', ['stylelint', 'jshint']);

  // Build language pack
  grunt.registerTask('build-lang', '', function () {

    /*jshint ignore:start*/
    const {execSync} = require('child_process');
    /*jshint ignore:end*/

    var pot = grunt.config.get('pkg.name').toLowerCase() + '.pot';
    
    console.log("Building language file as ./languages/" + pot);
    
    execSync('touch ./languages/source/' + pot); // Make sure it exists, if we're going to remove (for broken builds)
    execSync('rm ./languages/source/' + pot); // Remove existing

    execSync('find ./Idno ./templates -type f -regex ".*\.php" | sort | php vendor/mapkyca/known-language-tools/buildpot.php >> ./languages/source/' + pot); // Build from idno core
    execSync('echo ./known.php | php vendor/mapkyca/known-language-tools/buildpot.php >> ./languages/source/' + pot); // Build from console
  });

  // Default task(s).
  grunt.registerTask('build-js', ['concat', 'babel', 'terser']);
  grunt.registerTask('build-css', ['sass']);
  grunt.registerTask('default', ['build-js', 'build-css', 'build-lang', 'modernizr', 'test']);
};
