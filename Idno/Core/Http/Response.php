<?php

namespace Idno\Core\Http {

    use Symfony\Component\HttpFoundation\Response as SymfonyResponse;


    class Response extends SymfonyResponse{

        public function setJsonContent($content)
        {
            $this->headers->set('Content-Type', 'application/json');
            $this->setContent(json_encode($content));
            return $this;
        }

    }
}