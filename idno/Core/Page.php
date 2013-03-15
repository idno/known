<?php

    /**
     * Handles pages in the system (and, by extension, the idno API).
     * 
     * Developers shoudld extend the getContent, postContent and dataContent 
     * methods as follows:
     * 
     * getContent: echoes HTML to the page
     * 
     * postContent: handles content submitted to the page (assuming that form
     * elements were correctly signed)
     * 
     * @package idno
     * @subpackage core
     */

	namespace Idno\Core {
	
	    class Page extends \Idno\Common\Component {
		
		/**
		 * Internal function used to handle GET requests.
		 * Performs some administration functions and hands off to
		 * getContent().
		 */
		function get() {
		    site()->session()->APIlogin();
		    $this->getContent();
		}
		
		/**
		 * Internal function used to handle POST requests.
		 * Performs some administration functions, checks for the 
		 * presence of a POST token, and hands off to postContent().
		 * 
		 * @param $forward boolean If this is set to true, forward the page; otherwise return data.
		 */
		function post($forward = true) {
		    if (Action::validateToken('', false)) {
			site()->session()->APIlogin();
			$this->postContent($forward);
		    }
		}
		
		/**
		 * Internal function used to handle PUT requests.
		 * Performs some administration functions, checks for the 
		 * presence of a form token, and hands off to postContent().
		 * 
		 * @param $forward boolean If this is set to true, forward the page; otherwise return data.
		 */
		function put($forward = true) {
		    if (Action::validateToken('', false)) {
			site()->session()->APIlogin();
			$this->putContent($forward);
		    }
		}
		
		/**
		 * Internal function used to handle DELETE requests.
		 * Performs some administration functions, checks for the 
		 * presence of a form token, and hands off to postContent().
		 * 
		 * @param $forward boolean If this is set to true, forward the page; otherwise return data.
		 */
		function delete($forward = true) {
		    if (Action::validateToken('', false)) {
			site()->session()->APIlogin();
			$this->deleteContent($forward);
		    }
		}
		
		/**
		 * Automatically matches JSON/XMLHTTPRequest GET requests.
		 * Sets the template to JSON and then calls get().
		 */
		function get_xhr() {
		    site()->template()->setTemplateType('json');
		    $this->get();
		}
		
		/**
		 * Automatically matches JSON/XMLHTTPRequest POST requests.
		 * Sets the template to JSON and then calls post().
		 */
		function post_xhr() {
		    site()->template()->setTemplateType('json');
		    $this->post(false);
		}
		
		/**
		 * Automatically matches JSON/XMLHTTPRequest PUT requests.
		 * Sets the template to JSON and then calls put().
		 */
		function put_xhr() {
		    site()->template()->setTemplateType('json');
		    $this->put(false);
		}
		
		/**
		 * Automatically matches JSON/XMLHTTPRequest PUT requests.
		 * Sets the template to JSON and then calls delete().
		 */
		function delete_xhr() {
		    site()->template()->setTemplateType('json');
		    $this->delete(false);
		}
		
		/**
		 * To be extended by developers
		 */
		function getContent() {}
		
		/**
		 * To be extended by developers
		 */
		function postContent($forward = true) {}
		
		/**
		 * To be extended by developers
		 */
		function putContent($forward = true) {}
		
		/**
		 * To be extended by developers
		 */
		function deleteContent($forward = true) {}
		
	    }
	    
	}

