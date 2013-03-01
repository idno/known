<?php

    /**
     * Base Idno class
     * 
     * @package idno
     * @subpackage core
     */

	namespace Idno\Core {
	    
	    class Idno extends \Idno\Common\Component {
		
		public $db;
		public $config;
		public $session;
		public $template;
		public $dispatcher;
		public $pagehandlers;
		public static $site;
		
		function init() {
		    self::$site = $this;
		    $this->config = new Config();
		    $this->db = new DataConcierge();
		    $this->session = new Session();
		    $this->template = new Template();
		    $this->dispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
		}
		
		function registerpages() {
		    $this->addPageHandler('/', '\Idno\Core\IdnoPageHandler');
		}
		
		/**
		 * Return the database layer loaded as part of this site
		 * @return \Idno\Core\DataConcierge
		 */

		    function &db() { return $this->db; }
		    
		/**
		 * Return the event dispatcher loaded as part of this site
		 * @return \Symfony\Component\EventDispatcher\EventDispatcher 
		 */
		    
		    function &events() { return $this->dispatcher; }
		    
		/**
		 * Shortcut to trigger an event: supply the event name and 
		 * (optionally) an array of data, and get a variable back.
		 * 
		 * @param string $eventName The name of the event to trigger
		 * @param array $data Data to pass to the event
		 * @return mixed 
		 */
		    
		    function triggerEvent($eventName, $data = array()) {
			$event = new Event($data);
			$this->events()->dispatch($eventName, $event);
			return $event->response();
		    }

		/**
		 * Helper function that returns the current configuration object
		 * for this site (or a configuration setting value)
		 * 
		 * @param The configuration setting value to retrieve (optional)
		 * 
		 * @return \Idno\Core\Config
		 */
		    function &config($setting = false) { 
			if ($setting === false)
			    return $this->config; 
			else
			    return $this->config->$setting;
		    }

		/**
		 * Return the session handler associated with this site
		 * @return \Idno\Core\Session
		 */

		    function &session() { return $this->session; }
		    
		/** 
		 * Return the template handler associated with this site
		 * @return \Idno\Core\Template
		 */
		    
		    function &template() { return $this->template; }
		    
		/**
		 * Tells the system that callable $listener wants to be notified when
		 * event $event is triggered. $priority is an optional integer
		 * that specifies order priority; the higher the number, the earlier
		 * in the chain $listener will be notified.
		 * 
		 * @param string $event
		 * @param callable $listener 
		 * @param int $priority
		 */
		    
		    function addEventHook($event, $listener, $priority = 0) {
			if (is_callable($listener))
			    $this->dispatcher->addListener($event, $listener, $priority);
		    }
		    
		/**
		 * Registers a page handler for a given pattern, using Toro
		 * page handling syntax
		 * 
		 * @param string $pattern The pattern to match
		 * @param callable $handler The handler callable that will serve the page
		 */
		    
		    function addPageHandler($pattern, $handler) {
			if (class_exists($handler))
			    $this->pagehandlers[$pattern] = $handler;
		    }
		
	    }
	    
	    /**
	     * Default class to serve the homepage
	     */
	    class IdnoPageHandler {
		
		// Handle GET requests to the homepage
		
		function get() {
		    $feed = \Idno\Entities\ActivityStreamPost::get();
		    $t = \Idno\Core\site()->template();
		    $t->__(array(

				'title' => \Idno\Core\site()->config()->title,
				'body' => $t->__(array(
						    'feed' => $feed
						))->draw('pages/home'),

			))->drawPage();
		}
		
	    }
		
	    /**
	     * Helper function that returns the current site object
	     * @return Idno\Core\Idno
	     */
		function &site() {
		    return \Idno\Core\Idno::$site;
		}

	}