<?php

    /**
     * Service discovery (via webfinger) class
     * 
     * @package idno
     * @subpackage core
     */

	namespace Idno\Core {
	    
	    class Webfinger extends \Idno\Common\Component {
		
		function init() {}
		
		function registerpages() {
		    site()->addPageHandler('/\.well-known/', '\Idno\Core\WebfingerPageHandler');
		    site()->addPageHandler('/banana/', '\Idno\Core\WebfingerPageHandler');
		}
		
	    }
	    
	    class WebfingerPageHandler {
		
		function get() {
		    
		    echo '!';
		    
		}
		
	    }
	    
	}