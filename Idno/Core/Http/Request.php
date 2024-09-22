<?php

namespace Idno\Core\http {

    use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
    use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;


    class Request extends SymfonyRequest{

        public static function createFromSymfonyRequest(SymfonyRequest $request)
        {
            $newRequest = new self(
                $request->query->all(),
                $request->request->all(),
                $request->attributes->all(),
                $request->cookies->all(),
                $request->files->all(),
                $request->server->all(),
                $request->getContent()
            );

            return $newRequest;
        }

        public static function createFromPSR7Request(\Psr\Http\Message\ServerRequestInterface $psrRequest)
        {
            $httpFoundationFactory = new HttpFoundationFactory();
            $symfonyRequest = $httpFoundationFactory->createRequest($psrRequest);
            return self::createFromSymfonyRequest($symfonyRequest);
            
        }

    }
}