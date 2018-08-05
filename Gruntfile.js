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
	}
    });

// Load the plugin that provides the "uglify" task.
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');

// Default task(s).
    grunt.registerTask('default', ['uglify', 'cssmin']);
};