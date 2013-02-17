<?php

    /**
     * Template management class
     * 
     * @package idno
     * @subpackage core
     */

	namespace Idno\Core {
	
	    class Template extends \Bonita\Templates {
		
		/**
		 * On construction, detect the template type
		 */
		function __construct($template = false) {
		    if (!($template instanceof Template)) {
			$this->detectTemplateType();
		    }
		    return parent::__construct($template);
		}
	    }
	    
	}