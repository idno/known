module.exports = function(grunt) {
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		banner: '/*! <%= pkg.title || pkg.name %> - v<%= pkg.version %> - ' +
			'<%= grunt.template.today("yyyy-mm-dd") %>\n' +
			'<%= pkg.homepage ? "* " + pkg.homepage + "\\n" : "" %>' +
			'* Copyright (c) <%= grunt.template.today("yyyy") %> <%= pkg.author %>;' +
			' Licensed <%= _.pluck(pkg.licenses, "type").join(", ") %> */\n' ,

		license: 
			'/* ========================================================================' + '\n' +

			'* Extends Bootstrap v3.1.1' + '\n' + '\n' +
			'* Copyright (c) <2014> eBay Software Foundation' + '\n' +  '\n' +
			'* All rights reserved.' + '\n' +   '\n' +
			'* Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:' + '\n' +'\n' +
			'* Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.' + '\n' +'\n' +
			'* Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.' + '\n' +'\n' +
			'* Neither the name of eBay or any of its subsidiaries or affiliates nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.' + '\n' +'\n' +
			'* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.' + '\n' +'\n' +
			'* ======================================================================== */' + '\n',

		jshint: {
			options: {
				jshintrc: ".jshintrc"
			},
			files: {
				src: [
					'src/js/functions.js',
					'src/js/modal.js',
					'src/js/dropdown.js',
					'src/js/tab.js',
					'src/js/collapse.js',
					'src/js/carousel.js'
				],
			}			
		},

	    concat: {
	      options: {
	        banner: '<%= license %>  \n \n (function($) { \n  "use strict"; \n',
	        separator: '\n',
	        stripBanners: false,
			footer: '\n\n })(jQuery);'
	      },
			bootstrap: {
				src: [
					'src/js/functions.js',
					'src/js/modal.js',
					'src/js/dropdown.js',
					'src/js/tab.js',
					'src/js/collapse.js',
					'src/js/carousel.js'
				],
	        	dest: 'plugins/js/bootstrap-accessibility.js'
	       	} 
	    },

		uglify: {
			options: {
				banner: '<%= banner %>',
				// beautify: true,
				mangle: false
			},
			dist: {
				files: {
					'plugins/js/bootstrap-accessibility.min.js': 'plugins/js/bootstrap-accessibility.js',
					'plugins/js/bootstrap-accessibility_1.0.3.min.js': 'plugins/js/bootstrap-accessibility_1.0.3.js'
				}
			}
		},

		compass: {
			dist: {
				options: {
					sassDir: 'src/sass',
					cssDir: 'plugins/css',
					environment: 'production',
					outputStyle: 'compressed'
				}
			}
		},		

	});

	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-compass');

	grunt.registerTask('test', ['jshint']);
	grunt.registerTask('js', ['jshint', 'concat', 'uglify']);
	grunt.registerTask('css', 'compass');

	grunt.registerTask('default', ['jshint', 'concat', 'uglify', 'compass']);
};
