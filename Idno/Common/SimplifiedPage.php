<?php

/**
 * Simplified Page Class
 * 
 * Demonstrates how the Page class could be refactored to reduce complexity
 * by using the HttpMethodHandler and separating concerns
 *
 * @package    idno
 * @subpackage common
 */

namespace Idno\Common {

    use Idno\Core\HttpMethodHandler;
    use Idno\Entities\User;

    abstract class SimplifiedPage extends Component
    {
        private HttpMethodHandler $httpHandler;
        private bool $forward = true;
        private string $referrer = '';
        private array $data = [];
        private int $response = 200;
        private array $arguments = [];
        private bool $isPermalinkPage = false;
        private ?Entity $entity = null;
        private bool $xhr = false;
        private User|false $owner = false;
        private array $assets = [];

        public function __construct()
        {
            parent::__construct();
            $this->httpHandler = new HttpMethodHandler($this);
        }

        public function init(): void
        {
            if (!defined('KNOWN_UNIT_TEST')) {
                header('X-Powered-By: https://withknown.com');
                header('X-Clacks-Overhead: GNU Terry Pratchett');
                header('X-Known-Build-Fingerprint: ' . \Idno\Core\TokenProvider::truncateToken(\Idno\Core\Version::fingerprint()));
            }

            $this->handleTemplateType();
            $this->setCurrentPageInSite();
            $this->setReferrer();
            $this->setupExceptionHandler();
            
            \Idno\Core\Idno::site()->embedded();
            \Idno\Core\Idno::site()->events()->triggerEvent('page/ready');
        }

        /**
         * HTTP Method Handlers - delegated to HttpMethodHandler
         */
        public function get(): void
        {
            $this->httpHandler->handleGet(func_get_args());
        }

        public function get_xhr(): void
        {
            $this->httpHandler->handleGet(func_get_args(), true);
        }

        public function post(): void
        {
            $this->httpHandler->handlePost(func_get_args());
        }

        public function post_xhr(): void
        {
            $this->httpHandler->handlePost(func_get_args(), true);
        }

        public function put(): void
        {
            $this->httpHandler->handlePut(func_get_args());
        }

        public function put_xhr(): void
        {
            $this->httpHandler->handlePut(func_get_args(), true);
        }

        public function delete(): void
        {
            $this->httpHandler->handleDelete(func_get_args());
        }

        public function delete_xhr(): void
        {
            $this->httpHandler->handleDelete(func_get_args(), true);
        }

        public function head(): void
        {
            \Idno\Core\Idno::site()->session()->publicGatekeeper();
            \Idno\Core\Idno::site()->template()->autodetectTemplateType();
            $this->parseJSONPayload();

            $arguments = func_get_args();
            if (!empty($arguments)) {
                $this->arguments = $arguments;
            }

            \Idno\Core\Idno::site()->events()->triggerEvent('page/head', [
                'page_class' => get_called_class(),
                'arguments' => $arguments
            ]);

            $this->getContent();
            http_response_code($this->response);
        }

        public function head_xhr(): void
        {
            $this->xhr = true;
            $this->head();
        }

        public function options(): void
        {
            header('Access-Control-Allow-Methods: ' . implode(', ', [
                'GET', 'POST', 'HEAD', 'OPTIONS', 'PUT', 'DELETE'
            ]));
            header('Access-Control-Max-Age: 86400');
            http_response_code(204);
        }

        /**
         * Abstract methods to be implemented by subclasses
         */
        abstract public function getContent(): void;
        
        public function postContent()
        {
            $this->setResponse(501);
        }

        public function putContent()
        {
            $this->setResponse(501);
        }

        public function deleteContent()
        {
            $this->setResponse(501);
        }

        /**
         * Input handling
         */
        public function getInput(string $name, $default = null, callable $filter = null)
        {
            if (empty($name)) {
                return null;
            }

            $value = \Idno\Core\Input::getInput($name, $default, $filter);
            
            if ($value === null && isset($this->data[$name])) {
                $value = $this->data[$name];
            }

            if ($value === null && $default !== null) {
                $value = $default;
            }

            if ($value !== null && $filter && is_callable($filter) && empty($value)) {
                $value = call_user_func($filter, $name, $value);
            }

            return $value;
        }

        public function setInput(string $name, $value): void
        {
            if (!empty($name)) {
                $this->data[$name] = $value;
            }
        }

        /**
         * Response handling
         */
        public function setResponse(int $code): void
        {
            $this->response = $code;
            http_response_code($this->response);
        }

        public function response(): int
        {
            return $this->response;
        }

        /**
         * Navigation and forwarding
         */
        public function forward(string $location = '', bool $exit = true): void
        {
            if (empty($location)) {
                $location = \Idno\Core\Idno::site()->config()->getDisplayURL();
            }

            if (!$this->forward) {
                return;
            }

            if (\Idno\Core\Idno::site()->template()->getTemplateType() != 'default') {
                $location = \Idno\Core\Idno::site()->template()->getURLWithVar(
                    '_t',
                    \Idno\Core\Idno::site()->template()->getTemplateType(),
                    $location
                );
            }

            if ($exit) {
                \Idno\Core\Idno::site()->session()->finishEarly();
            }

            if (\Idno\Core\Idno::site()->session()->isAPIRequest()) {
                echo json_encode(['location' => $location]);
            } elseif (!\Idno\Core\Idno::site()->session()->isAPIRequest() || $this->response == 200) {
                header('Location: ' . $location);
            }

            if ($exit) {
                exit;
            }
        }

        public function forwardToLogin(string $fwd = '', bool $string = false): string|void
        {
            $url = \Idno\Core\Idno::site()->config()->getDisplayURL() . 'session/login/?fwd=' . 
                   \Idno\Core\Webservice::encodeValue($fwd);
            
            if ($string) {
                return $url;
            }
            
            $this->forward($url);
        }

        /**
         * Security gatekeepers
         */
        public function gatekeeper(): void
        {
            if (!\Idno\Core\Idno::site()->session()->isLoggedIn()) {
                $this->deniedContent();
            }
        }

        public function adminGatekeeper(): void
        {
            if (!\Idno\Core\Idno::site()->session()->isLoggedIn() || 
                !\Idno\Core\Idno::site()->session()->currentUser()->isAdmin()) {
                $this->deniedContent();
            }
        }

        public function createGatekeeper(): void
        {
            if (!\Idno\Core\Idno::site()->canWrite()) {
                $this->deniedContent();
            }
            $this->gatekeeper();
        }

        public function reverseGatekeeper(): void
        {
            if (\Idno\Core\Idno::site()->session()->isLoggedIn()) {
                $this->deniedContent();
            }
        }

        public function sslGatekeeper(): void
        {
            if (!static::isSSL() && 
                empty(\Idno\Core\Idno::site()->config()->ignore_ssl_gatekeeper) && 
                \Idno\Core\Idno::site()->config()->hasSSL()) {
                
                $url = str_replace('http://', 'https://', $this->currentUrl());
                header("HTTP/1.1 307 Temporary Redirect");
                header("Location: $url");
                exit;
            }
        }

        /**
         * Error handling
         */
        public function deniedContent(string $title = ''): void
        {
            $this->setResponse(403);
            header_remove('X-Known-CSRF-Ts');
            header_remove('X-Known-CSRF-Token');

            $t = \Idno\Core\Idno::site()->template();
            $t->__(['body' => $t->draw('pages/403'), 'title' => $title])->drawPage();
            exit;
        }

        public function noContent(): void
        {
            $this->setResponse(404);
            header_remove('X-Known-CSRF-Ts');
            header_remove('X-Known-CSRF-Token');

            $t = \Idno\Core\Idno::site()->template();
            $t->__([
                'body' => $t->draw('pages/404'),
                'title' => \Idno\Core\Idno::site()->language()->_('This page can\'t be found.')
            ])->drawPage();
            exit;
        }

        public function exception(\Exception $e): void
        {
            $this->setResponse(500);
            \Idno\Core\Idno::site()->logging()->critical($e->getMessage() . " [" . $e->getFile() . ":" . $e->getLine() . "]");

            $t = \Idno\Core\Idno::site()->template();
            $t->__([
                'body' => $t->__(['exception' => $e])->draw('pages/500'),
                'title' => 'Exception'
            ])->drawPage();
            exit;
        }

        /**
         * Utility methods
         */
        public function currentUrl(bool $tokenise = false): string|array
        {
            $url = parse_url(\Idno\Core\Idno::site()->config()->url);
            $url['path'] = $_SERVER['REQUEST_URI'];

            return $tokenise ? $url : static::buildUrl($url);
        }

        public static function buildUrl(array $url): string
        {
            $page = ($url['scheme'] ?? '') ? $url['scheme'] . "://" : '//';
            
            if (!empty($url['user'])) {
                $page .= $url['user'];
                if (!empty($url['pass'])) {
                    $page .= ":" . $url['pass'];
                }
                $page .= "@";
            }

            $page .= $url['host'];
            
            if (!empty($url['port'])) {
                $page .= ":" . $url['port'];
            }

            $page .= $url['path'];
            
            if (!empty($url['query'])) {
                $page .= "?" . $url['query'];
            }
            
            if (!empty($url['fragment'])) {
                $page .= "#" . $url['fragment'];
            }

            return $page;
        }

        public static function isSSL(): bool
        {
            return ($_SERVER['HTTPS'] ?? '') === '1' ||
                   strtolower($_SERVER['HTTPS'] ?? '') === 'on' ||
                   ($_SERVER['SERVER_PORT'] ?? '') === '443' ||
                   strtolower($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
        }

        /**
         * Private helper methods
         */
        private function handleTemplateType(): void
        {
            if ($template = $this->getInput('_t')) {
                if (\Idno\Core\Idno::site()->template()->templateTypeExists($template)) {
                    \Idno\Core\Idno::site()->template()->setTemplateType($template);
                }
            }
        }

        private function setCurrentPageInSite(): void
        {
            \Idno\Core\Idno::site()->setCurrentPage($this);
        }

        private function setReferrer(): void
        {
            $this->referrer = $_SERVER['HTTP_REFERER'] ?? '';
            $_SERVER['HTTP_REFERER'] = $_SERVER['HTTP_REFERER'] ?? '';
        }

        private function setupExceptionHandler(): void
        {
            set_exception_handler(function ($exception) {
                $page = \Idno\Core\Idno::site()->currentPage();
                if (!empty($page)) {
                    $page->exception($exception);
                } else {
                    \Idno\Core\site()->logging()->error($exception->getMessage());
                }
            });
        }

        public function parseJSONPayload(): void
        {
            // Handle form JSON input
            if (!empty($_REQUEST['json'])) {
                $json = trim($_REQUEST['json']);
                $json = str_replace('[]"', '"', $json);
                if ($parsed = @json_decode($json, true)) {
                    $this->data = array_merge($parsed, $this->data);
                }
            }

            // Handle raw JSON input
            if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] != 'GET') {
                $body = @file_get_contents('php://input');
                $body = trim($body);
                $body = str_replace('[]"', '"', $body);

                if (!empty($body)) {
                    if ($parsed = @json_decode($body, true)) {
                        $this->data = array_merge($parsed, $this->data);
                    }
                }
            }
        }

        // Getters and setters
        public function arguments(): array { return $this->arguments; }
        public function data(): array { return $this->data; }
        public function xhr(): bool { return $this->xhr; }
        public function referrer(): string { return $this->referrer; }
        
        public function setEntity(?Entity $entity): void { $this->entity = $entity; }
        public function getEntity(): ?Entity { return $this->entity; }
        public function removeEntity(): void { $this->entity = null; }
        
        public function setPermalink(bool $status = true, Entity $entity = null): void
        {
            $this->isPermalinkPage = $status;
            if ($status && $entity) {
                $this->setEntity($entity);
            }
        }
        
        public function isPermalink(): bool { return $this->isPermalinkPage; }
        
        public function setOwner($user): void
        {
            if ($user instanceof \Idno\Entities\User) {
                $this->owner = $user;
            }
        }
        
        public function getOwner(): User|false { return $this->owner; }
    }
}