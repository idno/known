<?php

    /**
     * Event class to handle data transport during event triggering
     * 
     * @package idno
     * @subpackage core
     */

     namespace Idno\Core {
	
	 class Event extends \Symfony\Component\EventDispatcher\Event {
	     
	     protected $data = array();
	     protected $dispatcher = null;
	     protected $response = true;
	     
	     function __construct($data = array()) {
		 $this->data = $data;
		 $this->dispatcher = site()->dispatcher();
	     }
	     
	     /**
	      * Retrieve data associated with an event
	      * @return mixed
	      */
	     function &data() {
		 return $this->data;
	     }
	     
	     /**
	      * Retrieve the response variable associated with this event
	      * @return type 
	      */
	     function &response() {
		 return $this->response;
	     }
	     
	 }
	 
     }