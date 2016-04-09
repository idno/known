<?php

/**
 * php-mf2 test suite
 * CLI usage from the tests/test-suite/ directory: php test-suite.php
 * @see https://github.com/tobiastom/tests
 *
 * This will look through the specified directory for suite.json files
 * that represent a suite of tests. Then, for each suite the sub-directories
 * will be parsed for input.html, output.json, and test.json files. Each
 * test will then be executed. If a test fails, a message is displayed along
 * with the parsed output (array format) and the expected output (array format).
 *
 * Individual test suites may be run by calling TestSuite->runSuite($path)
 * where $path is the file path to the suite.json file.
 */

namespace Mf2\Parser\TestSuite;

use Mf2\Parser;
use Mf2;

require dirname(__DIR__) . '/../vendor/autoload.php';

class TestSuite
{
	private $path = '';

	private $suites;

	private $tests_total = 0;

	private $tests_passed = 0;

	private $tests_failed = 0;

	/**
	 * This method constructs the TestSuite
	 * @param string $path: path to test-suite-data 
	 * @access public
	 */
	public function __construct($path = './test-suite-data/')
	{
		$this->path = $path;
	} # end method __construct()


	/**
	 * This method runs the test suite
	 * @param array 
	 * @access public
	 * @return bool
	 */
	public function start()
	{
		$directory = new \RecursiveDirectoryIterator($this->path);
		$iterator = new \RecursiveIteratorIterator($directory);
		$this->suites = new \RegexIterator($iterator, '/^.+suite\.json$/i', \RecursiveRegexIterator::GET_MATCH);

		foreach ( $this->suites as $suite )
		{
			$this->runSuite(reset($suite));
			// echo "\n";
		}

		echo sprintf('Total tests: %d', $this->tests_total), "\n";
		echo sprintf('Passed: %d', $this->tests_passed), "\n";
		echo sprintf('Failed: %d', $this->tests_failed), "\n";

		return TRUE;
	} # end method start()


	/**
	 * This method handles running a test suite
	 * @param string $path: path to the suite's JSON file 
	 * @access public
	 * @return bool
	 */
	public function runSuite($path)
	{
		$suite = json_decode(file_get_contents($path));
		echo sprintf('Running %s.', $suite->name), "\n";

		$iterator = new \DirectoryIterator(dirname($path));

		# loop: each file in the test suite
		foreach ( $iterator as $file )
		{	

			# if: file is a sub-directory and not a dot-directory
			if ( $file->isDir() && !$file->isDot() )
			{
				$this->tests_total++;

				$path_of_test = $file->getPathname() . '/';

				$test = json_decode(file_get_contents($path_of_test . 'test.json'));
				$input = file_get_contents($path_of_test . 'input.html');
				$expected_output = json_decode(file_get_contents($path_of_test . 'output.json'), TRUE);

				$parser = new Parser($input, '', TRUE);
				$output = $parser->parse(TRUE);

				# if: test passed
				if ( $output['items'] === $expected_output['items'] )
				{
					// echo '.'; # can output a dot for successful tests
					$this->tests_passed++;
				}
				# else: test failed
				else
				{
					echo sprintf('"%s" failed.', $test->name), "\n\n";
					echo sprintf('Parsed: %s', print_r($output, TRUE)), "\n";
					echo sprintf('Expected: %s', print_r($expected_output, TRUE)), "\n";
					$this->tests_failed++;
				} # end if

			} # end if

		} # end loop

		return TRUE;
	} # end method runSuite()

}

$TestSuite = new TestSuite();
$TestSuite->start(); # run all test suites

// Alternately, run a specific suite
// $TestSuite->runSuite('./test-suite-data/adr/suite.json');
