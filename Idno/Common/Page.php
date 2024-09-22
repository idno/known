<?php

    /**
     * Handles pages in the system (and, by extension, the idno API).
     *
     * Developers should extend the getContent, postContent and dataContent
     * methods as follows:
     *
     * getContent: set content for the page using \Idno\Core\Idno::site()->response()->setContent()
     *
     * postContent: handles content submitted to the page (assuming that form
     * elements were correctly signed)
     *
     * @package    idno
     * @subpackage core
     */

namespace Idno\Common {

    use Idno\Entities\User;
    use Idno\Core\Http\Response;

    abstract class Page extends \Idno\Common\Component
    {

        // Property that defines whether this page may forward to
        // other pages. True by default.
        private $forward = true;

        // Where was this page forwarded from
        private $referrer = '';

        // Property intended to store parsed data from JSON magic input
        // variable
        private $data = [];

        // Stores the response code that we'll be sending back. Can be
        // changed with setResponse
        private $response = 200;

        // Stores arguments given to page handlers, for parsing of regular
        // expression matches
        public $arguments = [];

        // Is this the canonical permalink page for an object? Defaults
        // to no, but you can use $this->setPermalink() to change this
        private $isPermalinkPage = false;

        // If this page is associated with a particular entity, we'll
        // store it here so we can access it in shell templates etc
        private $entity = null;

        // Is this an XmlHTTPRequest (AJAX) call?
        public $xhr = false;

        // Who owns this page, anyway?
        private $owner = false;

        // Page assets that can be registered and set by plugins (javascript, css, etc)
        private $assets = array();

        function init()
        {
            if (!defined('KNOWN_UNIT_TEST')) { // Don't do header stuff in unit tests
                \Idno\Core\Idno::site()->response()->headers->set('X-Powered-By', 'https://withknown.com');
                \Idno\Core\Idno::site()->response()->headers->set('X-Clacks-Overhead', 'GNU Terry Pratchett');
                \Idno\Core\Idno::site()->response()->headers->set('X-Known-Version', \Idno\Core\Version::version());

            }
            if ($template = $this->getInput('_t')) {
                if (\Idno\Core\Idno::site()->template()->templateTypeExists($template)) {
                    \Idno\Core\Idno::site()->template()->setTemplateType($template);
                }
            }
            \Idno\Core\Idno::site()->setCurrentPage($this);

            // Set referrer, and ensure it's not blank
            $this->referrer =\Idno\Core\Idno::site()->request()->server->get('HTTP_REFERER')??'';
            \Idno\Core\Idno::site()->request()->server->set('HTTP_REFERER',$this->referrer);

            // Default exception handler
            set_exception_handler(
                function ($exception) {
                    $page = \Idno\Core\Idno::site()->currentPage();
                    if (!empty($page)) {
                        $page->exception($exception);

                    } else {
                        \Idno\Core\site()->logging()->error($exception->getMessage());
                    }

                }
            );

            \Idno\Core\Idno::site()->embedded();

            // Trigger an event when a page is initialised, and currentPage is available
            \Idno\Core\Idno::site()->events()->triggerEvent('page/ready');
        }

        /**
         * Retrieves input.
         *
         * @param  string  $name    Name of the input variable
         * @param  mixed   $default A default return value if no value specified (default: null)
         * @param  boolean $filter  Whether or not to filter the variable for safety (default: null), you can pass
         *                          a callable method, function or enclosure with a definition like
         *                          function($name, $value), which will return the filtered result.
         * @return mixed
         */
        function getInput($name, $default = null, callable $filter = null)
        {
            if (!empty($name)) {
                $value = null;
                $request = \Idno\Core\Input::getInput($name, $default, $filter);
                if ($request !== null) {
                    $value = $request;
                } else if (isset($this->data[$name])) {
                    $value = $this->data[$name];
                }
                if (($value===null) && ($default!==null)) {
                    $value = $default;
                }
                if (!$value!==null) {
                    if (isset($filter) && is_callable($filter) && empty($request)) {
                        $value = call_user_func($filter, $name, $value);
                    }

                    // TODO, we may want to add some sort of system wide default filter for when $filter is null

                    return $value;
                }
            }

            return null;
        }

        function exception($e)
        {
            $this->setResponse(500);

            \Idno\Core\Idno::site()->logging()->critical($e->getMessage() . " [".$e->getFile().":".$e->getLine()."]");

            $stats = \Idno\Core\Idno::site()->statistics();
            if (!empty($stats)) {
                $stats->increment("error.exception");
            }

            try {
                \Idno\Core\Logging::oopsAlert($e->getMessage() . " [".$e->getFile().":".$e->getLine()."]", get_class($e));
            } catch (Exception $ex) {
                error_log($ex->getMessage());
            }

            $t = \Idno\Core\Idno::site()->template();
            $content = $t->__(array('body' => $t->__(array('exception' => $e))->draw('pages/500'), 'title' => 'Exception'))->drawPage(false);
            \Idno\Core\Idno::site()->response()->setContent($content);
        }

        /**
         * Set the response code for the page. Note: this will be overridden
         * if the main system response code is already not 200
         *
         * @param int $code
         */
        function setResponse(int $code)
        {
            $code           = (int)$code;
            $this->response = $code;
            \Idno\Core\Idno::site()->response()->setStatusCode($code);
           
        }

        /**
         * Return the current response code for the page.
         *
         * @return int
         */
        function response():int
        {
            return $this->response;
        }

        function head_xhr()
        {
            \Idno\Core\Idno::site()->session()->publicGatekeeper();

            \Idno\Core\Idno::site()->template()->autodetectTemplateType();

            $arguments = func_get_args();
            if (!empty($arguments)) { $this->arguments = $arguments;
            }
            $this->xhr = true;

            $this->head();
        }

        function head()
        {
            \Idno\Core\Idno::site()->session()->publicGatekeeper();

            \Idno\Core\Idno::site()->template()->autodetectTemplateType();

            $this->parseJSONPayload();

            $arguments = func_get_args();
            if (!empty($arguments)) { $this->arguments = $arguments;
            }

            \Idno\Core\Idno::site()->events()->triggerEvent('page/head', array('page_class' => get_called_class(), 'arguments' => $arguments));

            // Triggering GET content to call all the appropriate headers (web server should truncate the head request from body).
            // This is the only way we can generate accurate expires and content length etc, but could be done more efficiently
            $this->getContent();

            //if (http_response_code() != 200)
            $this->setResponse($this->response);
        }

        /**
         * Finds a JSON payload associated with the current page request
         * and parses any variables into $this->data
         */
        function parseJSONPayload()
        {

            // First, let's see if we've been sent anything in form input
            if (!empty($_REQUEST['json'])) {
                $json = trim($_REQUEST['json']);
                $json = str_replace('[]"', '"', $json); // Fake PHP's array conversion
                if ($parsed = @json_decode($json, true)) {
                    $this->data = array_merge($parsed, $this->data());
                }
            }

            if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] != 'GET') {
                $body = @file_get_contents('php://input');
                $body = trim($body);
                $body = str_replace('[]"', '"', $body); // Fake PHP's array conversion

                if (!empty($body)) {
                    if ($parsed = @json_decode($body, true)) {
                        $this->data = array_merge($parsed, $this->data());
                    }
                }
            }

        }

        /**
         * Return the arguments sent to the page via regular expression
         *
         * @return array
         */
        function &arguments() : array
        {
            return $this->arguments;
        }

        /**
         * Provide access to page data
         *
         * @return array
         */
        function &data() : array
        {
            return $this->data;
        }

        /**
         * Is this an XHR page or not
         *
         * @return bool
         */
        function xhr(): bool
        {
            return $this->xhr;
        }

        /**
         * To be extended by developers
         */
        function getContent()
        {
            $this->setResponse(501);
        }

        /**
         * Automatically matches JSON/XMLHTTPRequest GET requests.
         * Sets the template to JSON and then calls get().
         */
        function get_xhr()
        {
            \Idno\Core\Idno::site()->session()->publicGatekeeper();

            \Idno\Core\Idno::site()->template()->autodetectTemplateType();

            $arguments = func_get_args();
            if (!empty($arguments)) { $this->arguments = $arguments;
            }
            $this->xhr = true;
            $this->get();
        }

        /**
         * Internal function used to handle GET requests.
         * Performs some administration functions and hands off to
         * getContent().
         */
        function get($params = array())
        {

            \Idno\Core\Idno::site()->session()->publicGatekeeper();

            \Idno\Core\Idno::site()->template()->autodetectTemplateType();

            $this->parseJSONPayload();

            $arguments = func_get_args();
            if (!empty($arguments)) { $this->arguments = $arguments;
            }

            \Idno\Core\Idno::site()->events()->triggerEvent('page/head', array('page' => $this));
            \Idno\Core\Idno::site()->events()->triggerEvent('page/get', array('page_class' => get_called_class(), 'arguments' => $arguments));

            $this->getContent();

            if (\Idno\Core\Idno::site()->response()->getStatusCode() != 200) {
                \Idno\Core\Idno::site()->response()->setStatusCode($this->response);
            }
        }

        protected function debugLogToken()
        {

            $ts = "";
            if (empty(\Idno\Core\Idno::site()->request()->request->get('__bTs'))) {
                \Idno\Core\Idno::site()->logging()->error("__bTs timestamp is missing");
            } else {
                $ts = \Idno\Core\Idno::site()->request()->request->get('__bTs');
            }

            $ta = "";
            if (empty(\Idno\Core\Idno::site()->request()->request->get('__bTa'))) {
                \Idno\Core\Idno::site()->logging()->warning("__bTa action is missing");
            } else {
                $ta = \Idno\Core\Idno::site()->request()->request->get('__bTa');
            }

            $tk = "";
            if (empty(\Idno\Core\Idno::site()->request()->request->get('__bTk'))) {
                \Idno\Core\Idno::site()->logging()->error("__bTk token is missing");
            } else {
                $tk = \Idno\Core\Idno::site()->request()->request->get('__bTk');
            }

            $debug = [
                'time' => $ts,
                'token' => \Idno\Core\TokenProvider::truncateToken($tk),
                'action' => $ta,
                'site_secret' => \Idno\Core\TokenProvider::truncateToken(\Idno\Core\Idno::site()->config()->site_secret),
                'session_id' => \Idno\Core\TokenProvider::truncateToken(session_id()),
                'expected-token' => \Idno\Core\TokenProvider::truncateToken(
                    \Idno\Core\Bonita\Forms::token($ta, $ts)
                ),
                'expected-token-no-action' => \Idno\Core\TokenProvider::truncateToken(
                    \Idno\Core\Bonita\Forms::token('', $ts)
                )
            ];
            \Idno\Core\Idno::site()->logging()->error("Token was not valid:\n\nDebug:". print_r($debug, true));
        }

        /**
         * Automatically matches JSON/XMLHTTPRequest POST requests.
         * Sets the template to JSON and then calls post().
         */
        function post_xhr()
        {
            \Idno\Core\Idno::site()->session()->publicGatekeeper();

            \Idno\Core\Idno::site()->template()->autodetectTemplateType();

            $arguments = func_get_args();
            if (!empty($arguments)) { $this->arguments = $arguments;
            }
            $this->xhr     = true;
            $this->forward = false;
            $this->post();
        }

        /**
         * Internal function used to handle POST requests.
         * Performs some administration functions, checks for the
         * presence of a POST token, and hands off to postContent().
         */
        function post()
        {
            \Idno\Core\Idno::site()->session()->publicGatekeeper();

            \Idno\Core\Idno::site()->template()->autodetectTemplateType();

            $arguments = func_get_args();
            if (!empty($arguments)) { $this->arguments = $arguments;
            }

            \Idno\Core\Idno::site()->events()->triggerEvent('page/head', array('page' => $this));
            \Idno\Core\Idno::site()->events()->triggerEvent('page/post', array('page_class' => get_called_class(), 'arguments' => $arguments));

            if (\Idno\Core\Idno::site()->session()->isAPIRequest() || \Idno\Core\Idno::site()->actions()->validateToken($this->currentUrl(), false) || \Idno\Core\Idno::site()->actions()->validateToken('', false)) {
                $this->parseJSONPayload();
                $return = $this->postContent();
            } else {

                $this->debugLogToken();

                \Idno\Core\Idno::site()->logging()->error(\Idno\Core\Idno::site()->language()->_('Invalid token.'));
                \Idno\Core\Idno::site()->session()->addErrorMessage(\Idno\Core\Idno::site()->language()->_('Invalid token.'));
            }

            if (\Idno\Core\Idno::site()->session()->isAPIRequest()) {

                // If postContent hasn't forwarded itself, and returns null, then balance of probabilities is something went wrong.
                // Either way, it's not safe to forward to the site root since this spits back json encoded front page and a 200 response.
                // API currently doesn't explicitly handle this situation (bad), but we don't want to forward (worse). Some plugins will still
                // forward to / in some situations, these will need rewriting.
                if ($return === null) {
                    if (\Idno\Core\Idno::site()->response()->getStatusCode() == 200) {
                        $this->setResponse(400);
                    }

                    // Say something, if nothing has been said
                    $messages = \Idno\Core\Idno::site()->session()->getMessages();
                    if (empty($messages)) {
                        \Idno\Core\Idno::site()->session()->addErrorMessage(\Idno\Core\Idno::site()->language()->_("Something went wrong."));
                    }

                    $t = \Idno\Core\Idno::site()->template();
                    $content = $t->drawPage(false);
                    \Idno\Core\Idno::site()->response()->setContent($content);

                } else {
                    // We have a return value, and response hasn't been explicitly set. Assume false is error, everything else is ok
                    if (($return === false) && (\Idno\Core\Idno::site()->response()->getStatusCode() == 200)) {
                        $this->setResponse(400);
                    }

                    $t = \Idno\Core\Idno::site()->template();
                    $content = $t->__(['result' => $return])->drawPage(false);
                    \Idno\Core\Idno::site()->response()->setContent($content);
                }
            } else {
                $this->forward(); // If we haven't forwarded yet, do so (if we can)
            }

            //if (http_response_code() != 200) {
            \Idno\Core\Idno::site()->response()->setStatusCode($this->response);
            //}
        }

        /**
         * To be extended by developers
         */
        function postContent()
        {
            $this->setResponse(501);
        }

        /**
         * Default handling of OPTIONS (mostly to handle CORS)
         */
        function options()
        {

            \Idno\Core\Idno::site()->response()->headers->set('Access-Control-Allow-Methods', implode(
                    ', ', [
                    'GET',
                    'POST',
                    'HEAD',
                    'OPTIONS',
                    'PUT',
                    'DELETE'
                    ]
                ));
           
            
            \Idno\Core\Idno::site()->response()->headers->set('Access-Control-Max-Age', '86400');

            \Idno\Core\Idno::site()->response()->setStatusCode(204);

        }

        /**
         * Return the referrer, or an empty string
         *
         * @return string
         */
        function referrer() : string
        {
            return $this->referrer;
        }

        /**
         * If this page is allowed to forward, send a header to move
         * the browser on. Otherwise, do nothing
         *
         * @param string $location Location to forward to (eg "/foo/bar")
         * @param bool   $exit     If set to true (which it is by default), execution finishes once the header is sent.
         */
        function forward(string $location = '', bool $exit = true)
        {
            if (empty($location)) {
                $location = \Idno\Core\Idno::site()->config()->getDisplayURL();
            }
            if (!empty($this->forward)) {
                if (\Idno\Core\Idno::site()->template()->getTemplateType() != 'default') {
                    $location = \Idno\Core\Idno::site()->template()->getURLWithVar('_t', \Idno\Core\Idno::site()->template()->getTemplateType(), $location);
                }
                if ($exit) {
                    \Idno\Core\Idno::site()->session()->finishEarly();
                }

                /*
                 * TODO: find a more granular way to do this. But some Known functions depend on
                 * redirection to other sites (eg a Known hub).

                if (!Entity::isLocalUUID($location)) {
                    throw new \RuntimeException(\Idno\Core\Idno::site()->language()->_('Attempted to redirect page to a non local URL.'));
                }
                */

                $call_trace = null;
                $config = \Idno\Core\Idno::site()->config();
                if (!empty($config->forward_trace) && $config->forward_trace) {

                    $trace = debug_backtrace();
                    if (!empty($trace[1])) {

                        $call_trace = "";

                        if (!empty($trace[0])) {
                            $call_trace .= "Forward at {$trace[0]['file']}:{$trace[0]['line']}";
                        }

                        if (!empty($trace[1])) {
                            $trace_file = 'UNKNOWN';
                            if (!empty($trace[1]['file'])) { $trace_file = $trace[1]['file'];
                            }
                            $trace_line = 'xxx';
                            if (!empty($trace[1]['line'])) { $trace_line = $trace[1]['line'];
                            }

                            $call_trace .= ", called by {$trace[1]['function']} in {$trace_file}:{$trace_line}";
                        }

                        $log = \Idno\Core\Idno::site()->logging();
                        if (!empty($log)) {
                            $log->debug($call_trace);
                        }
                    }
                }

                if (\Idno\Core\Idno::site()->session()->isAPIRequest()) {
                    $location = [
                        'location' => $location
                    ];
                    if (!empty($call_trace)) {
                        $location['trace'] = $call_trace;
                    }
                    \Idno\Core\Idno::site()->response()->setJsonContent($location);
                } elseif (!\Idno\Core\Idno::site()->session()->isAPIRequest() || $this->response == 200) {
                    if (!empty($call_trace)) { header('X-Known-Forward-Trace: ' . $call_trace);
                    }

                    \Idno\Core\Idno::site()->redirect($location);
                }

                if ($exit) {
                    exit;
                }
            }
        }

        /**
         * Forwards to login page with optional forward param
         *
         * @param string $fwd
         * @param bool   $string If set to true, will return a string instead of forwarding
         */
        function forwardToLogin($fwd = '', $string = false)
        {
            $url = \Idno\Core\Idno::site()->config()->getDisplayURL() . 'session/login/?fwd=' . \Idno\Core\Webservice::encodeValue($fwd);
            if ($string) {
                return $url;
            }
            $this->forward($url);
        }

        /**
         * Automatically matches JSON/XMLHTTPRequest PUT requests.
         * Sets the template to JSON and then calls put().
         */
        function put_xhr()
        {
            \Idno\Core\Idno::site()->session()->publicGatekeeper();

            \Idno\Core\Idno::site()->template()->autodetectTemplateType();

            $arguments = func_get_args();
            if (!empty($arguments)) { $this->arguments = $arguments;
            }
            $this->xhr     = true;
            $this->forward = false;
            $this->put();
        }

        /**
         * Internal function used to handle PUT requests.
         * Performs some administration functions, checks for the
         * presence of a form token, and hands off to postContent().
         */
        function put()
        {
            \Idno\Core\Idno::site()->session()->publicGatekeeper();

            \Idno\Core\Idno::site()->template()->autodetectTemplateType();

            $arguments = func_get_args();
            if (!empty($arguments)) { $this->arguments = $arguments;
            }

            \Idno\Core\Idno::site()->events()->triggerEvent('page/head', array('page' => $this));
            \Idno\Core\Idno::site()->events()->triggerEvent('page/put', array('page_class' => get_called_class(), 'arguments' => $arguments));

            if (\Idno\Core\Idno::site()->session()->isAPIRequest() || \Idno\Core\Idno::site()->actions()->validateToken($this->currentUrl(), false) || \Idno\Core\Idno::site()->actions()->validateToken('', false)) {
                $this->parseJSONPayload();
                $return = $this->putContent();
            } else {
                $this->debugLogToken();

                throw new \Idno\Exceptions\SecurityException(\Idno\Core\Idno::site()->language()->_('The page you were on timed out.'));
            }

            if (\Idno\Core\Idno::site()->session()->isAPIRequest()) {

                // Ensure we always get a meaningful response from the api
                if ($return === null) {
                    if (\Idno\Core\Idno::site()->response()->getStatusCode() == 200) {
                        $this->setResponse(400);
                    }

                    // Say something, if nothing has been said
                    $messages = \Idno\Core\Idno::site()->session()->getMessages();
                    if (empty($messages)) {
                        \Idno\Core\Idno::site()->session()->addErrorMessage(\Idno\Core\Idno::site()->language()->_("Couldn't say anything about execution, probably something went wrong"));
                    }

                    $t = \Idno\Core\Idno::site()->template();
                    $content = $t->drawPage(false);
                    \Idno\Core\Idno::site()->response()->setContent($content);

                } else {
                    // We have a return value, and response hasn't been explicitly set. Assume false is error, everything else is ok
                    if (($return === false) && (\Idno\Core\Idno::site()->response()->getStatusCode() == 200)) {
                        $this->setResponse(400);
                    }

                    $t = \Idno\Core\Idno::site()->template();
                    $content = $t->__(['result' => $return])->drawPage(false);
                    \Idno\Core\Idno::site()->response()->setContent($content);
                }

            } else {
                $this->forward(); // If we haven't forwarded yet, do so (if we can)
            }

            if (\Idno\Core\Idno::site()->response()->getStatusCode() != 200) {
                \Idno\Core\Idno::site()->response()->setStatusCode($this->response);
            }
        }

        /**
         * To be extended by developers
         */
        function putContent()
        {
            $this->setResponse(501);
        }

        /**
         * Automatically matches JSON/XMLHTTPRequest PUT requests.
         * Sets the template to JSON and then calls delete().
         */
        function delete_xhr()
        {
            \Idno\Core\Idno::site()->session()->publicGatekeeper();

            \Idno\Core\Idno::site()->template()->autodetectTemplateType();

            $arguments = func_get_args();
            if (!empty($arguments)) { $this->arguments = $arguments;
            }
            $this->xhr     = true;
            $this->forward = false;
            $this->delete();
        }

        /**
         * Internal function used to handle DELETE requests.
         * Performs some administration functions, checks for the
         * presence of a form token, and hands off to postContent().
         */
        function delete()
        {
            \Idno\Core\Idno::site()->session()->publicGatekeeper();

            \Idno\Core\Idno::site()->template()->autodetectTemplateType();

            $arguments = func_get_args();
            if (!empty($arguments)) { $this->arguments = $arguments;
            }

            \Idno\Core\Idno::site()->events()->triggerEvent('page/head', array('page' => $this));
            \Idno\Core\Idno::site()->events()->triggerEvent('page/delete', array('page_class' => get_called_class(), 'arguments' => $arguments));

            if (\Idno\Core\Idno::site()->session()->isAPIRequest() || \Idno\Core\Idno::site()->actions()->validateToken($this->currentUrl(), false) || \Idno\Core\Idno::site()->actions()->validateToken('', false)) {
                $this->parseJSONPayload();
                $return = $this->deleteContent();
            } else {
                $this->debugLogToken();

                throw new \Idno\Exceptions\SecurityException(\Idno\Core\Idno::site()->language()->_('The page you were on timed out.'));
            }

            if (\Idno\Core\Idno::site()->session()->isAPIRequest()) {

                // Ensure we always get a meaningful response from the api
                if ($return === null) {
                    if (\Idno\Core\Idno::site()->response()->getStatusCode() == 200) {
                        $this->setResponse(400);
                    }

                    // Say something, if nothing has been said
                    $messages = \Idno\Core\Idno::site()->session()->getMessages();
                    if (empty($messages)) {
                        \Idno\Core\Idno::site()->session()->addErrorMessage(\Idno\Core\Idno::site()->language()->_("Couldn't say anything about execution, probably something went wrong"));
                    }

                    $t = \Idno\Core\Idno::site()->template();
                    $content = $t->drawPage(false);
                    \Idno\Core\Idno::site()->response()->setContent($content);

                } else {
                    // We have a return value, and response hasn't been explicitly set. Assume false is error, everything else is ok
                    if (($return === false) && (\Idno\Core\Idno::site()->response()->getStatusCode() == 200)) {
                        $this->setResponse(400);
                    }

                    $t = \Idno\Core\Idno::site()->template();
                    $content = $t->__(['result' => $return])->drawPage(false);
                    \Idno\Core\Idno::site()->response()->setContent($content);
                }

            } else {
                $this->forward(); // If we haven't forwarded yet, do so (if we can)
            }

            if (\Idno\Core\Idno::site()->response()->getStatusCode() != 200) {
                \Idno\Core\Idno::site()->response()->setStatusCode($this->response);
            }
        }

        /**
         * To be extended by developers
         */
        function deleteContent()
        {
            $this->setResponse(501);
        }

        /**
         *
         */
        function webmention()
        {
            \Idno\Core\Idno::site()->session()->publicGatekeeper();

            \Idno\Core\Idno::site()->template()->autodetectTemplateType();

            $this->forward = false;
            //$this->webmentionContent();
        }

        /**
         * Called when there's been a successful webmention call to the given page.
         * To be extended by developers.
         *
         * @param  string $source          The source URL (i.e., third-party site URL)
         * @param  string $target          The target URL (i.e., this page)
         * @param  array  $source_response The Webservice response from fetching the source page
         * @param  array  $source_mf2      The full, parsed Microformats 2 content of the source URL
         * @return bool true if this page accepts webmentions
         */
        function webmentionContent($source, $target, $source_response, $source_mf2)
        {
            return false;
        }

        /**
         * Page handler for when a resource has disappeared.
         */
        function goneContent()
        {
            $this->setResponse(410);
            \Idno\Core\Idno::site()->response()->setStatusCode($this->response);

            \Idno\Core\Idno::site()->response()->headers->remove('X-Known-CSRF-Ts');
            \Idno\Core\Idno::site()->response()->headers->remove('X-Known-CSRF-Token');


            $t = \Idno\Core\Idno::site()->template();
            $content = $t->__(array('body' => $t->draw('pages/410'), 'title' => \Idno\Core\Idno::site()->language()->_('This content isn\'t here.')))->drawPage(false);
            \Idno\Core\Idno::site()->response()->setContent($content);
            \Idno\Core\Idno::site()->sendResponse();
        }

        /**
         * Page handler for when a resource doesn't exist.
         */
        function noContent()
        {
            $this->setResponse(404);
            
            // http_response_code($this->response);
            \Idno\Core\Idno::site()->response()->headers->remove('X-Known-CSRF-Ts');
            \Idno\Core\Idno::site()->response()->headers->remove('X-Known-CSRF-Token');


            $t = \Idno\Core\Idno::site()->template();
            $content = $t->__(array('body' => $t->draw('pages/404'), 'title' => \Idno\Core\Idno::site()->language()->_('This page can\'t be found.')))->drawPage(false);
            \Idno\Core\Idno::site()->response()->setContent($content);
            \Idno\Core\Idno::site()->sendResponse();
        }

        /**
         * Flushes content to the browser and continues page working asynchronously.
         * Depricate ?
         */
        function flushBrowser()
        {
            header('Connection: close');
            header('Content-length: ' . (string)ob_get_length());

            @ob_end_flush();            // Return output to the browser
            @ob_end_clean();
            @flush();
        }

        /**
         * Placed in pages to ensure that a user is logged in and able
         * to create content. Returns a 403 and forwards to the home page if
         * the user can't create content.
         */
        function createGatekeeper()
        {
            if (!\Idno\Core\Idno::site()->canWrite()) {
                $this->deniedContent();
            }
            $this->gatekeeper();
        }

        /**
         * You can't see this.
         */
        function deniedContent($title = '')
        {
            $this->setResponse(403);
            \Idno\Core\Idno::site()->response()->setStatusCode($this->response);
            \Idno\Core\Idno::site()->response()->headers->remove('X-Known-CSRF-Ts');
            \Idno\Core\Idno::site()->response()->headers->remove('X-Known-CSRF-Token');

            $t = \Idno\Core\Idno::site()->template();
            $content = $t->__(array('body' => $t->draw('pages/403'), 'title' => $title))->drawPage(false);
            \Idno\Core\Idno::site()->response()->setContent($content);
        }

        /**
         * Ensure this can only be called via xhr.
         */
        function xhrGatekeeper()
        {
            if (!$this->xhr) {
                $this->deniedContent();
            }
        }

        /**
         * Ensure that a page is passed valid tokens.
         * This is useful for api endpoints.
         */
        function tokenGatekeeper()
        {
            $url = $this->currentUrl();
            $bits = explode('?', $url);
            $url = $bits[0];
            if (!\Idno\Core\Idno::site()->actions()->validateToken($url, false)) {
                $this->deniedContent();
            }
        }

        /**
         * Placed in pages to ensure that only logged-in users can
         * get at them. Sets response code 401 and tries to forward
         * to the front page.
         */
        function gatekeeper()
        {
            if (!\Idno\Core\Idno::site()->session()->isLoggedIn()) {
                $this->deniedContent();
            }
        }

        /**
         * Placed in pages to ensure that only logged-out users can
         * get at them. Sets response code 401 and tries to forward
         * to the front page.
         */
        function reverseGatekeeper()
        {
            if (\Idno\Core\Idno::site()->session()->isLoggedIn()) {
                $this->deniedContent();
            }
        }

        /**
         * Placed in pages to ensure that only logged-in site administrators can
         * get at them. Sets response code 401 and tries to forward
         * to the front page.
         */
        function adminGatekeeper()
        {
            $ok = false;
            if (\Idno\Core\Idno::site()->session()->isLoggedIn()) {
                if (\Idno\Core\Idno::site()->session()->currentUser()->isAdmin()) {
                    $ok = true;
                }
            }
            if (!$ok) {
                $this->deniedContent();
            }
        }

        /**
         * Sets the entity on the page to the specified object
         *
         * @param object $entity
         */
        function setEntity($entity)
        {
            $this->entity = $entity;
        }

        /**
         * Returns the entity associated with this page, if it exists
         *
         * @return \Idno\Common\Entity|null
         */
        function getEntity(): ?Entity
        {
            return $this->entity;
        }

        /**
         * Removes any entity associated with this page
         */
        function removeEntity()
        {
            $this->entity = null;
        }

        /**
         * Is this page a permalink for an object? This should be set to 'true'
         * if it is. Optionally, we can also associate the page with the object here.
         *
         * @param bool   $status Is this a permalink? Defaults to 'true'
         * @param object $entity Optionally, an entity this page is associated with
         */
        function setPermalink(bool $status = true, Entity $entity = null)
        {
            $this->isPermalinkPage = $status;
            if ($status && $entity) { $this->setEntity($entity);
            }
        }

        /**
         * Is this page a permalink for an object?
         *
         * @return bool
         */
        function isPermalink()
        {
            return $this->isPermalinkPage;
        }

        /**
         * Force connection over SSL.
         * If a page is requested over HTTP, this function will issue a 307 redirect to force
         * the connection over TLS. 307 is used to preserve POST data on a web services call.
         */
        function sslGatekeeper()
        {
            if (!static::isSSL() && empty(\Idno\Core\Idno::site()->config()->ignore_ssl_gatekeeper) && \Idno\Core\Idno::site()->config()->hasSSL()) {

                $url = str_replace('http://', 'https://', $this->currentUrl());

                \Idno\Core\Idno::site()->redirect($url);

            }
        }

        /**
         * Has the page been requested over SSL?
         *
         * @return boolean
         */
        static function isSSL()
        {
            if (\Idno\Core\Idno::site()->request()->isSecure()) {
                    return true;
            } else if (\Idno\Core\Idno::site()->request()->server->has('SERVER_PORT') && (\Idno\Core\Idno::site()->request()->server->get('SERVER_PORT') == '443')) {
                return true;
            }

            if (\Idno\Core\Idno::site()->request()->server->has('HTTP_X_FORWARDED_PROTO') && strtolower(\Idno\Core\Idno::site()->request()->server->get('HTTP_X_FORWARDED_PROTO')) == 'https') {
                return true;
            }

            return false;
        }

        /**
         * Return the full URL of the current page.
         *
         * @param  $tokenise bool If true then an exploded tokenised version is returned.
         * @return url|array
         */
        public function currentUrl($tokenise = false)
        {
            $url         = parse_url(\Idno\Core\Idno::site()->config()->url);
            $url['path'] = \Idno\Core\Idno::site()->request()->getPathInfo();

            if ($tokenise) {
                return $url;
            }

            return self::buildUrl($url);
        }

        /**
         * Helper function to see if the given Known base path matches the current page URL.
         * This is useful for setting active on menus in subdirectory installs.
         *
         * @param  type $path Path, relative to the Known base
         * @return bool
         */
        public function doesPathMatch($path)
        {

            $path = parse_url(\Idno\Core\Idno::site()->config()->url . trim($path, ' /') . '/');
            $current = $this->currentUrl(true);

            return trim($path['path'], ' /') == trim($current['path'], ' /');
        }

        /**
         * Construct a URL from array components (basically an implementation of http_build_url() without PECL.
         *
         * @param  array $url
         * @return string
         */
        public static function buildUrl(array $url)
        {
            if (!empty($url['scheme'])) {
                $page = $url['scheme'] . "://";
            } else {
                $page = '//';
            }

            // user/pass
            if ((isset($url['user'])) && !empty($url['user'])) {
                $page .= $url['user'];
            }
            if ((isset($url['pass'])) && !empty($url['pass'])) {
                $page .= ":" . $url['pass'];
            }
            if (!empty($url['user']) || !empty($url['pass'])) {
                $page .= "@";
            }

            $page .= $url['host'];

            if ((isset($url['port'])) && ($url['port'])) {
                $page .= ":" . $url['port'];
            }

            $page .= $url['path'];

            if ((isset($url['query'])) && ($url['query'])) {
                $page .= "?" . $url['query'];
            }

            if ((isset($url['fragment'])) && ($url['fragment'])) {
                $page .= "#" . $url['fragment'];
            }

            return $page;
        }

        /**
         * Sets an input value that can subsequently be retrieved by getInput.
         * Note that actual input variables (i.e., those supplied by GET or POST
         * variables) will still take precedence.
         *
         * @param string $name
         * @param mixed  $value
         */
        function setInput($name, $value)
        {
            if (!empty($name)) {
                $this->data[$name] = $value;
            }
        }

        /**
         * Get the referrer information for the current page.
         */
        function getReferrer()
        {

            $referrer = \Idno\Core\Idno::site()->request()->server->get('HTTP_REFERER');

            if (empty($referrer)) {
                // TODO: Try other ways - e.g. for nginx
            }

            return $referrer;
        }

        /**
         * Detects whether the current web browser accepts the given content type.
         *
         * @param  string $contentType     The MIME content type.
         * @param  bool   $ignore_priority If true, the 'q' parameter is ignored and the method returns true if
         *                                 $contentType appears anywhere in the accept header (original
         *                                 behaviour), otherwise it'll return true only if it's the highest
         *                                 value parameter. See #1622
         * @return bool
         */
        function isAcceptedContentType($contentType, $ignore_priority = false)
        {

            $headers = self::getallheaders();
            if (!empty($headers)) {

                if ($ignore_priority) {
                    if (!empty($headers['Accept'])) {
                        if (substr_count($headers['Accept'], $contentType)) { return true;
                        }
                    }
                } else {
                    if (!empty($headers['Accept'])) {
                        $types = [];
                        $accepts = explode(',', $headers['Accept']);

                        foreach ($accepts as $accept) {
                            $q = 1; // default value

                            if (strpos($accept, ';q=')) {
                                list($accept, $q) = explode(';q=', $accept);
                            }

                            while (in_array($q, $types)) { $q -= 000000000001;
                            } // fudge to give equal values order priority. TODO: do this a better way

                            $types[$accept] = $q;
                        }

                        arsort($types);

                        foreach ($types as $type => $value) {
                            return $type == $contentType;
                        }
                    }
                }
            }

            return false;
        }

        /**
         * Shim for running on nginx, which doesn't provide the
         * getallheaders function
         *
         * @return array
         */
        static function getallheaders()
        {
            $headers = array();
            foreach (\Idno\Core\Idno::site()->request()->headers->all() as $name => $value) {
                if (substr($name, 0, 14) == 'REDIRECT_HTTP_') {
                    $name = substr($name, 9);
                }
                if (substr($name, 0, 5) == 'HTTP_') {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }

            return $headers;
        }

        /**
         * Retrieve bearer token passed to this page, if any.
         *
         * @return string|null
         */
        public static function getBearerToken(): ?string
        {

            $headers = null;
            $serverheaders = \Idno\Common\Page::getallheaders();

            if (isset($serverheaders['Authorization'])) {
                $headers = trim($serverheaders["Authorization"]);
            } else if (isset($serverheaders['HTTP_AUTHORIZATION'])) {
                $headers = trim($serverheaders["HTTP_AUTHORIZATION"]);
            }

            if (!empty($headers)) {
                if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                    return trim($matches[1], '\'"');
                }
            }

            return null;
        }

        /**
         * Set or add a file asset.
         *
         * @param type $name  Name of the asset (e.g. 'idno', 'jquery')
         * @param type $class Class of asset (e.g. 'javascript', 'css')
         * @param type $value A URL or other value
         */
        public function setAsset(string $name, string $value, string $class)
        {
            if (!isset($this->assets) || !is_array($this->assets)) { $this->assets = array();
            }
            if (!isset($this->assets[$class]) || !is_array($this->assets)) { $this->assets[$class] = array();
            }

            $this->assets[$class][$name] = $value;
        }

        /**
         * Get assets of a given class.
         *
         * @param  type $class
         * @return array
         */
        public function getAssets(string $class)
        {
            if (isset($this->assets[$class])) {
                return $this->assets[$class];
            }

            return false;
        }

        /**
         * Get an icon for this page.
         */
        public function getIcon()
        {
            $icon = \Idno\Core\Idno::site()->config()->getStaticURL() . 'gfx/logos/logo_k.png';

            if (\Idno\Core\Idno::site()->config()->user_avatar_favicons) {
                if ($user = \Idno\Core\Idno::site()->currentPage()->getOwner()) {
                    if ($user instanceof \Idno\Entities\User) {
                        $icon = $user->getIcon();
                    }
                }
            } else if (\Idno\Core\Idno::site()->config()->share_backup_url) {
                $icon = \Idno\Core\Idno::site()->config()->share_backup_url;
            }

            return \Idno\Core\Idno::site()->events()->triggerEvent('icon', ['object' => $this], $icon);
        }

        /**
         * Retrieves the effective owner of this page, if one has been set
         *
         * @return bool|User
         */
        function getOwner()
        {
            if (!empty($this->owner)) {
                if ($this->owner instanceof \Idno\Entities\User) {
                    return $this->owner;
                }
            }

            return false;
        }

        /**
         * Sets the given user as owner of this page
         *
         * @param $user
         */
        function setOwner($user)
        {
            if ($user instanceof \Idno\Entities\User) {
                $this->owner = $user;
            }
        }

        /**
         * Set headers to ensure that the current page is not cached.
         */
        public function setNoCache()
        {
            \Idno\Core\Idno::site()->response()->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            \Idno\Core\Idno::site()->response()->headers->set('Cache-Control', 'post-check=0, pre-check=0', false);
            \Idno\Core\Idno::site()->response()->headers->set('Pragma', 'no-cache');
        }

        /**
         * Set the last updated header for this page.
         * Takes a unix timestamp and outputs it as RFC2616 date.
         *
         * @param int $timestamp Unix timestamp.
         */
        public function setLastModifiedHeader(int $timestamp)
        {
            \Idno\Core\Idno::site()->response()->headers->set('Last-Modified', \Idno\Core\Time::timestampToRFC2616($timestamp));
        }

        /**
         * Simplify if modified since checks.
         * Set a 304 not modified if If-Modified-Since header is less than the given timestamp.
         *
         * @param type $timestamp Timestamp to check
         */
        public function lastModifiedGatekeeper($timestamp)
        {
            $headers = self::getallheaders();
            if (isset($headers['If-Modified-Since'])) {
                if (strtotime($headers['If-Modified-Since']) <= $timestamp) {
                    \Idno\Core\Idno::site()->response()->setStatusCode(304);
                    \Idno\Core\Idno::site()->sendResponse();
                }
            }
        }

        /**
         * Return whether the current page URL matches the given regex string.
         *
         * @param type $regex_string URL string in the same format as the page handler definition.
         */
        public function matchUrl($regex_string)
        {
            $url = $this->currentUrl(true);

            $page = $url['path'];

            if ((isset($url['query'])) && ($url['query'])) {
                $page .= "?" . $url['query'];
            }

            if ((isset($url['fragment'])) && ($url['fragment'])) {
                $page .= "#" . $url['fragment'];
            }

            $url = $page;

            // Now we've got our page url, match it against regex
            return preg_match('#^/?' . $regex_string . '/?$#', $url);
        }

    }

}
