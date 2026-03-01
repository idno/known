/**
 * Gruntfile for Example
 */

module.exports = function (grunt) {
    // Project configuration.
    grunt.initConfig({
	pkg: grunt.file.readJSON('package.json'),
    });

    
// Build language pack (todo: find a cleaner way)
    grunt.registerTask('build-lang', '', function(){

	const { execSync } = require('child_process');

	var name = grunt.config.get('pkg.name').toLowerCase();

	console.log("Building language file for " + name);

	execSync('php ../../idno.php build-lang plugin:' + name, {stdio: 'inherit'});

    });

};