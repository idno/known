<?php

namespace Idno\Core {
    use \Idno\Core\http\Request;
    use \Idno\Core\Http\Response;
    class IdnoFoundation
    {

        private $request;
        private $response;

        public function __construct()
        {
            $this->request = Request::createFromGlobals();
            $this->response = new \Idno\Core\Http\Response();
        }

        public function &request()
        {
            return $this->request;
        }

        public function &response()
        {
            return $this->response;
        }

        function &sendResponse()
        {
            $this->response->send();

        }
    }
}