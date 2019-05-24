/**
 * Gruntfile for Known project.
 */

module.exports = function (grunt) {
  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    sass: {
	dist: {
	    files: {
	        'css/known.css': 'css/scss/known.scss',
		'css/known-simple.css': 'css/scss/known-simple.scss'
	    },
	    options: {
		sourcemap: 'none'
	    }
	}
    },
    uglify: {
      options: {
      },
      js: {
        files: {
          'js/known.min.js': [
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
          'js/service-worker.min.js': [
	      'js/src/ServiceWorker.js'
	  ]
        }
      }
    },
    cssmin: {
      target: {
        files: [{
          expand: true,
          cwd: 'css/',
          src: ['*.css', '!*.min.css'],
          dest: 'css/',
          ext: '.min.css'
        }]
      }
    },
    csslint: {
      options: {
        quiet: true,
        ids: false
      },
      src: ['css/*.css', '!*.min.css']
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
            tasks:  ['sass', 'cssmin', 'csslint']
        },
        js: {
            files: 'js/src/**/*.js',
            tasks:  ['uglify', 'jshint']
        }
    }

  });

// Load the plugins
  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-csslint');

// Tests
  grunt.registerTask('test', ['csslint', 'jshint']);

// Build language pack
  grunt.registerTask('build-lang', '', function () {

    const {execSync} = require('child_process');

    var pot = grunt.config.get('pkg.name').toLowerCase() + '.pot';
    
    console.log("Building language file as ./languages/" + pot);
    
    execSync('touch ./languages/source/' + pot); // Make sure it exists, if we're going to remove (for broken builds)
    execSync('rm ./languages/source/' + pot); // Remove existing

    execSync('find ./Idno -type f -regex ".*\.php" | php vendor/mapkyca/known-language-tools/buildpot.php >> ./languages/source/' + pot); // Build from idno core
    execSync('find ./templates -type f -regex ".*\.php" | php vendor/mapkyca/known-language-tools/buildpot.php >> ./languages/source/' + pot); // Build from templates
    execSync('echo ./known.php | php vendor/mapkyca/known-language-tools/buildpot.php >> ./languages/source/' + pot); // Build from console

  });

// Default task(s).
  grunt.registerTask('default', ['sass', 'cssmin', 'uglify']);
};
