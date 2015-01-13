/*global module:false*/
module.exports = function(grunt) {
  var browsers = grunt.file.readJSON("sauce-browsers.json");
  // Project configuration.
  grunt.initConfig({
    qunit: {
      files: ['test/**/*.html']
    },
    connect: {
      server: {
        options: {
          base: "",
          port: 9999
        }
      }
    },
    'saucelabs-qunit': {
        all: {
            options: {
                urls: ["http://127.0.0.1:9999/test/H5F.html"],
                tunnelTimeout: 5,
                build: process.env.TRAVIS_JOB_ID,
                concurrency: 2,
                browsers: browsers,
                testname: "qunit tests",
                tags: ["master"]
            }
        }
    },
    watch: {
      files: '<config:lint.files>',
      tasks: 'lint qunit'
    },
    jshint: {
      options: {
        jshintrc: '.jshintrc'
      },
      all: [
        'Gruntfile.js',
        'src/{,*/}*.js',
        'test/{,*/}*.js'
      ]
    },
    uglify: {
      options: {
        stripbanners: true,
        banner: '<%= banner%>',
        mangle: {
          except: ['H5F']
        },
        preserveComments: 'some'
      },
      dist: {
        src: [
          'src/H5F.js'
        ],
        dest: 'h5f.min.js'
      }
    }
  });

  // Load required contrib packages
  require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);

  // Default task.
  grunt.registerTask('default', ['jshint', 'qunit', 'uglify']);

  // Sauce labs CI task
  grunt.registerTask("sauce", ["connect", "saucelabs-qunit"]);
  
  // Travis CI task.
  grunt.registerTask('travis', ['jshint', 'sauce']);

  

};
