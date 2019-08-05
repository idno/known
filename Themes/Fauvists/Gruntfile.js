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
    });

    // Build your language file
    grunt.registerTask('build-lang', '', function(){
	
	const { execSync } = require('child_process');
	
	var pot = grunt.config.get('pkg.name').toLowerCase() + '.pot';
	
	console.log("Building language file as ./languages/" + pot);
	
	execSync('touch ./languages/' + pot); // Make sure it exists, if we're going to remove (for broken builds)
	execSync('rm ./languages/' + pot); // Remove existing
	
	execSync('find . -type f -regex ".*\.php" | php vendor/mapkyca/known-language-tools/buildpot.php >> ./languages/' + pot); 
	
    });

};