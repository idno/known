<?php

/**
 * HTTP Method Handler
 * 
 * Reduces code duplication in Page.php by centralizing HTTP method handling
 *
 * @package    idno
 * @subpackage core
 */

namespace Idno\Core {

    use Idno\Common\Page;

    class HttpMethodHandler
    {
        private $page;

        public function __construct(Page $page)
        {
            $this->page = $page;
        }

        /**
         * Handle GET requests with common logic
         */
        public function handleGet(array $arguments = [], bool $isXhr = false): void
        {
            \Idno\Core\Idno::site()->session()->publicGatekeeper();
            \Idno\Core\Idno::site()->template()->autodetectTemplateType();

            if (!empty($arguments)) {
                $this->page->arguments = $arguments;
            }

            if ($isXhr) {
                $this->page->xhr = true;
            }

            $this->page->parseJSONPayload();

            \Idno\Core\Idno::site()->events()->triggerEvent('page/head', ['page' => $this->page]);
            \Idno\Core\Idno::site()->events()->triggerEvent('page/get', [
                'page_class' => get_class($this->page),
                'arguments' => $arguments
            ]);

            $this->page->getContent();

            if (http_response_code() != 200) {
                http_response_code($this->page->response());
            }
        }

        /**
         * Handle POST requests with common logic
         */
        public function handlePost(array $arguments = [], bool $isXhr = false): void
        {
            \Idno\Core\Idno::site()->session()->publicGatekeeper();
            \Idno\Core\Idno::site()->template()->autodetectTemplateType();

            if (!empty($arguments)) {
                $this->page->arguments = $arguments;
            }

            if ($isXhr) {
                $this->page->xhr = true;
                $this->page->forward = false;
            }

            \Idno\Core\Idno::site()->events()->triggerEvent('page/head', ['page' => $this->page]);
            \Idno\Core\Idno::site()->events()->triggerEvent('page/post', [
                'page_class' => get_class($this->page),
                'arguments' => $arguments
            ]);

            $return = null;
            if ($this->validateRequest()) {
                $this->page->parseJSONPayload();
                $return = $this->page->postContent();
            } else {
                $this->handleInvalidToken();
            }

            $this->handlePostResponse($return, $isXhr);
        }

        /**
         * Handle PUT requests with common logic
         */
        public function handlePut(array $arguments = [], bool $isXhr = false): void
        {
            \Idno\Core\Idno::site()->session()->publicGatekeeper();
            \Idno\Core\Idno::site()->template()->autodetectTemplateType();

            if (!empty($arguments)) {
                $this->page->arguments = $arguments;
            }

            if ($isXhr) {
                $this->page->xhr = true;
                $this->page->forward = false;
            }

            \Idno\Core\Idno::site()->events()->triggerEvent('page/head', ['page' => $this->page]);
            \Idno\Core\Idno::site()->events()->triggerEvent('page/put', [
                'page_class' => get_class($this->page),
                'arguments' => $arguments
            ]);

            $return = null;
            if ($this->validateRequest()) {
                $this->page->parseJSONPayload();
                $return = $this->page->putContent();
            } else {
                $this->handleInvalidToken();
                return;
            }

            $this->handlePostResponse($return, $isXhr);
        }

        /**
         * Handle DELETE requests with common logic
         */
        public function handleDelete(array $arguments = [], bool $isXhr = false): void
        {
            \Idno\Core\Idno::site()->session()->publicGatekeeper();
            \Idno\Core\Idno::site()->template()->autodetectTemplateType();

            if (!empty($arguments)) {
                $this->page->arguments = $arguments;
            }

            if ($isXhr) {
                $this->page->xhr = true;
                $this->page->forward = false;
            }

            \Idno\Core\Idno::site()->events()->triggerEvent('page/head', ['page' => $this->page]);
            \Idno\Core\Idno::site()->events()->triggerEvent('page/delete', [
                'page_class' => get_class($this->page),
                'arguments' => $arguments
            ]);

            $return = null;
            if ($this->validateRequest()) {
                $this->page->parseJSONPayload();
                $return = $this->page->deleteContent();
            } else {
                $this->handleInvalidToken();
                return;
            }

            $this->handlePostResponse($return, $isXhr);
        }

        /**
         * Validate request tokens
         */
        private function validateRequest(): bool
        {
            return \Idno\Core\Idno::site()->session()->isAPIRequest() ||
                   \Idno\Core\Idno::site()->actions()->validateToken($this->page->currentUrl(), false) ||
                   \Idno\Core\Idno::site()->actions()->validateToken('', false);
        }

        /**
         * Handle invalid token
         */
        private function handleInvalidToken(): void
        {
            $this->debugLogToken();
            \Idno\Core\Idno::site()->logging()->error(\Idno\Core\Idno::site()->language()->_('Invalid token.'));
            \Idno\Core\Idno::site()->session()->addErrorMessage(\Idno\Core\Idno::site()->language()->_('Invalid token.'));
        }

        /**
         * Handle POST/PUT/DELETE response
         */
        private function handlePostResponse($return, bool $isXhr): void
        {
            if (\Idno\Core\Idno::site()->session()->isAPIRequest()) {
                $this->handleApiResponse($return);
            } else {
                $this->page->forward();
            }

            http_response_code($this->page->response());
        }

        /**
         * Handle API response
         */
        private function handleApiResponse($return): void
        {
            if ($return === null) {
                if (http_response_code() == 200) {
                    $this->page->setResponse(400);
                }

                $messages = \Idno\Core\Idno::site()->session()->getMessages();
                if (empty($messages)) {
                    \Idno\Core\Idno::site()->session()->addErrorMessage(
                        \Idno\Core\Idno::site()->language()->_("Something went wrong.")
                    );
                }

                $t = \Idno\Core\Idno::site()->template();
                echo $t->drawPage();
            } else {
                if (($return === false) && (http_response_code() == 200)) {
                    $this->page->setResponse(400);
                }

                $t = \Idno\Core\Idno::site()->template();
                echo $t->__(['result' => $return])->drawPage();
            }
        }

        /**
         * Debug token validation issues
         */
        private function debugLogToken(): void
        {
            $ts = $_REQUEST['__bTs'] ?? '';
            $ta = $_REQUEST['__bTa'] ?? '';
            $tk = $_REQUEST['__bTk'] ?? '';

            if (empty($ts)) {
                \Idno\Core\Idno::site()->logging()->error("__bTs timestamp is missing");
            }
            if (empty($ta)) {
                \Idno\Core\Idno::site()->logging()->warning("__bTa action is missing");
            }
            if (empty($tk)) {
                \Idno\Core\Idno::site()->logging()->error("__bTk token is missing");
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
            
            \Idno\Core\Idno::site()->logging()->error("Token was not valid:\n\nDebug:" . print_r($debug, true));
        }
    }
}