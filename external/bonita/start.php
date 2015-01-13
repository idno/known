<?php

	/*
	
		Bonita
	
		A simple templating engine for PHP 5
	
		Include this file from your PHP project to get started.
	
	
		@package Bonita
	
	*/
	
	// Bonita uses autoloading
		
		spl_autoload_register(function($class) {
		    $class = str_replace('\\','/',$class);
		    @include(dirname(__FILE__) . '/includes/'.$class.'.php');
		});
		
	// Set Bonita base path to the directory this file is in
		\Bonita\Main::$path = dirname(__FILE__);
		
	// Establish mobile 
		
	// Check for the existence of a cache file: if it exists, run it
	// (NB: right now, the cache mechanism is a definite @TODO)
		if (file_exists(\Bonita\Main::$path . '/paths.cache.php')) @include_once \Bonita\Main::$path . '/paths.cache.php';