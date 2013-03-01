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
		    site()->addPageHandler('/\.well\-known/webfinger/?', '\Idno\Core\WebfingerPageHandler');
		}
		
	    }
	    
	    class WebfingerPageHandler {
		
		function get() {
		    
		    if (!empty($_GET['resource'])) {
			$acct = $_GET['resource'];
			if (substr($acct,0,5) == 'acct:' && strlen($acct) > 8) {
			    $email = substr($acct,5);
			    if ($user = \Idno\Entities\User::getOne(array('email' => $email))) {
				site()->events()->dispatch('webfinger', new Event(array('user' => $user)));
			    }
			}
		    }
		    header('Content-type: application/json');
		    
		    
		}
		
	    }
	    
	}