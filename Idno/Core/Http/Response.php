<?php

namespace Idno\Core\Http {

    use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

    class Response extends SymfonyResponse{

        private bool $sendPsr7Response = false;
        private $psrHttpFactory = null;

        private $delaySend = false;

        public function setJsonContent($content)
        {
            $this->headers->set('Content-Type', 'application/json');
            $this->setContent(json_encode($content));
            return $this;
        }

        public function delaySend()
        {
            $this->delaySend = true;
        }

        public function isDelayed()
        {
            return $this->delaySend;
        }

        

    }
}