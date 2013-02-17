<?php

    /**
     * Template management class
     * 
     * @package idno
     * @subpackage core
     */

	namespace Idno\Core {
	
	    class Template extends \Idno\Common\Component {
		
		public $theme;
		
		/**
		 * On initialization, include Bonita template object as $this->theme
		 */
		function init() {
		    $this->theme = new \Bonita\Templates();
		    $this->theme->detectTemplateType();
		}
		
		/**
		 * Reference to this template's theme
		 * @return \Bonita\Templates
		 */
		function &theme() {
		    return $this->theme;
		}
		
	    }
	    
	}