/**
 * Gruntfile for Cherwell
 */

module.exports = function (grunt) {
    // Project configuration.
  grunt.initConfig({
	  pkg: grunt.file.readJSON('package.json'),
  });


  // Build language pack (todo: find a cleaner way)
  grunt.registerTask('build-lang', '', function(){

    const { execSync } = require('child_process');

    execSync('touch ./languages/cherwell.pot'); // Make sure it exists, if we're going to remove (for broken builds)
    execSync('rm ./languages/cherwell.pot'); // Remove existing

    execSync('find . -type f -regex ".*\.php" | php ../../languages/processfile.php >> ./languages/cherwell.pot');

  });

};
