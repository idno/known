<?php

    /**
     * idno loader and all-purpose conductor
     * 
     * @package idno
     * @subpackage core
     */

    // We're making heavy use of the Symfony ClassLoader to load our classes
	require_once(dirname(dirname(__FILE__)) . '/external/symfony/ClassLoader/UniversalClassLoader.php');
	$loader = new \Symfony\Component\ClassLoader\UniversalClassLoader();
	
    // Register our main namespaces (all idno classes adhere to the PSR-0 standard)

    // idno trunk classes (i.e., the main framework) are in /idno
	$loader->registerNamespace('Idno', dirname(dirname(__FILE__)));
    // idno plugins are located in /idno-plugins and must have their own namespace
	$loader->registerNamespace('Idno\\Plugins', dirname(dirname(__FILE__)) . '/idno-plugins');
	
    // Register our external namespaces (PSR-0 compliant modules that we love, trust and need)

    // Bonita is being used for templating
	$loader->registerNamespace('Bonita', dirname(dirname(__FILE__)) . '/external/bonita');
    // Symfony is used for routing, observer design pattern support, and a bunch of other fun stuff
	$loader->registerNamespace('Symfony\\Components', dirname(dirname(__FILE__)) . '/external/symfony');

	$loader->register();
	
	$idno = new Idno\Core\Idno();