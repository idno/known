<?php

    /**
     * All idno components inherit this base class
     * 
     * @package idno
     * @subpackage common
     */

	 namespace Idno\Common {

	     class Component {
		 
		 function __construct() {
		     $this->init();
		     $this->registerEventHooks();
		     $this->registerPages();
		 }
		 
		 /**
		  * Any initialization tasks to perform? This is the place to
		  * do it. Note that any page registration tasks should be
		  * performed using registerPages(), and any event hooks should 
		  * be performed using registerEventHooks().
		  */
		    function init() {}
		    
		 /**
		  * Here's your handy-dandy placeholder for registering any
		  * event hooks with the EventDispatcher.
		  * 
		  * Note that misc init functionality should be placed in the
		  * init() function, and page routing / models should be placed
		  * in registerPages().
		  */
		    function registerEventHooks() {}
		 
		 /**
		  * Registers any pages with the router. If components don't
		  * extend this function, no pages are registered. It's up to
		  * the components to either use a separate function to actually
		  * define the page action, or to use an anonymous function.
		  * Choices, people!
		  */
		    function registerPages() {}
		 
	     }
	     
	 }