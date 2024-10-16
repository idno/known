<?php

namespace Idno\Core {
    use \Symfony\Component\HttpFoundation\Request;
    use \Symfony\Component\HttpFoundation\Response;
    class IdnoFoundation
    {

        private $request;
        private $response;

        public function __construct()
        {
            $this->request = Request::createFromGlobals();
            $this->response = new Response();
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