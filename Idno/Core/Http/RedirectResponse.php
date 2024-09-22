<?php

namespace Idno\Core\http {


    class RedirectResponse extends Response
    {
        private string $targetUrl;


        public function __construct(string $url, int $status = 302, array $headers = [])
        {
            parent::__construct('', $status, $headers);

            $this->setTargetUrl($url);

            if (!$this->isRedirect()) {
                throw new \InvalidArgumentException(sprintf('The HTTP status code is not a redirect ("%s" given).', $status));
            }

            if (301 == $status && !\array_key_exists('cache-control', array_change_key_case($headers, \CASE_LOWER))) {
                $this->headers->remove('cache-control');
            }
        }

        /**
         * Returns the target URL.
         */
        public function getTargetUrl(): string
        {
            return $this->targetUrl;
        }

        /**
         * Sets the redirect target of this response.
         *
         * @return $this
         *
         * @throws \InvalidArgumentException
         */
        public function setTargetUrl(string $url): static
        {
            if ('' === $url) {
                throw new \InvalidArgumentException('Cannot redirect to an empty URL.');
            }

            $this->targetUrl = $url;

            $this->setContent(
                sprintf('<!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8" />
            <meta http-equiv="refresh" content="0;url=\'%1$s\'" />
    
            <title>Redirecting to %1$s</title>
        </head>
        <body>
            Redirecting to <a href="%1$s">%1$s</a>.
        </body>
    </html>', htmlspecialchars($url, \ENT_QUOTES, 'UTF-8'))
            );

            $this->headers->set('Location', $url);
            $this->headers->set('Content-Type', 'text/html; charset=utf-8');

            return $this;
        }
    }
}