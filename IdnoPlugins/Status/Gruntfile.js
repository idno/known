/**
 * Sample language Gruntfile.
 * 
 * Copy this to your Idno plugin root, rename to Gruntfile.js, and create a package.json 
 * with an appropriate "name" variable (usually your package namespace).
 */

module.exports = function (grunt) {
    
    // Project configuration.
    grunt.initConfig({
	pkg: grunt.file.readJSON('package.json'),
    });

    // Build your language file
    grunt.registerTask('build-lang', '', function(){

	const { execSync } = require('child_process');

	var name = grunt.config.get('pkg.name').toLowerCase();

	console.log("Building language file for " + name);

	execSync('php ../../idno.php build-lang plugin:' + name, {stdio: 'inherit'});

    });

};