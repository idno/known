/**
 * Gruntfile for Known project.
 */

module.exports = function (grunt) {
  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    uglify: {
      options: {
        banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n'
      },
      my_target: {
        files: {
          'js/default.min.js': 'js/default.js',
          'js/embeds.min.js': 'js/embeds.js',
          'js/image.min.js': 'js/image.js',
          'js/service-worker.min.js': 'js/service-worker.js',
          'js/templates/default/shell.min.js': 'js/templates/default/shell.js'
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
      src: ['css/*.css']
    },
    jshint: {
      // define the files to lint
      files: [
        'Gruntfile.js',
        'js/default.js',
        'js/embeds.js',
        'js/image.js',
        'js/service-worker.js',
        'js/templates/default/shell.js'
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
      files: ['<%= jshint.files %>'],
      tasks: ['jshint']
    }

  });

// Load the plugins
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

    execSync('touch ./languages/source/known.pot'); // Make sure it exists, if we're going to remove (for broken builds)
    execSync('rm ./languages/source/known.pot'); // Remove existing

    execSync('find ./Idno -type f -regex ".*\.php" | php ./languages/processfile.php >> ./languages/source/known.pot'); // Build from idno core
    execSync('find ./templates -type f -regex ".*\.php" | php ./languages/processfile.php >> ./languages/source/known.pot'); // Build from templates
    execSync('echo ./known.php | php ./languages/processfile.php >> ./languages/source/known.pot'); // Build from console

  });

// Default task(s).
  grunt.registerTask('default', ['uglify', 'cssmin']);
};
