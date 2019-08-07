/**
 * Sample language Gruntfile.
 * 
 * Copy this to your Known plugin root, rename to Gruntfile.js, and create a package.json 
 * with an appropriate "name" variable (usually your package namespace).
 */

module.exports = function (grunt) {

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        sass: {
            dist: {
                files: {
                    'css/default.css': 'css/scss/default.scss'
                },
                options: {
                    sourcemap: 'none'
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
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-csslint');
    
    
    grunt.registerTask('default', ['sass', 'cssmin']);

    // Build your language file
    grunt.registerTask('build-lang', '', function () {

        const {execSync} = require('child_process');

        var pot = grunt.config.get('pkg.name').toLowerCase() + '.pot';

        console.log("Building language file as ./languages/" + pot);

        execSync('touch ./languages/' + pot); // Make sure it exists, if we're going to remove (for broken builds)
        execSync('rm ./languages/' + pot); // Remove existing

        execSync('find . -type f -regex ".*\.php" | php vendor/mapkyca/known-language-tools/buildpot.php >> ./languages/' + pot);

    });

};