<?php

/**
 * URL Helper
 * 
 * Extracts URL-related functionality from Config.php for better separation of concerns
 *
 * @package    idno
 * @subpackage core
 */

namespace Idno\Core {

    use Idno\Common\Page;

    class UrlHelper
    {
        private $config;

        public function __construct(array $config)
        {
            $this->config = $config;
        }

        /**
         * Detect the base URL for the site
         */
        public function detectBaseURL(): string
        {
            // Use server name if available
            if (!empty($_SERVER['SERVER_NAME'])) {
                return $this->buildUrlFromServerName();
            }

            // Use domain from environment
            $domain = getenv('KNOWN_DOMAIN');
            if (!empty($domain)) {
                return $this->buildUrlFromDomain($domain);
            }

            // Default to root relative URLs
            return '/';
        }

        /**
         * Build URL from server name
         */
        private function buildUrlFromServerName(): string
        {
            $url = (Page::isSSL() ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'];

            // Add port if non-standard
            if (!empty($_SERVER['HTTP_X_FORWARDED_PORT'])) {
                if ($_SERVER['HTTP_X_FORWARDED_PORT'] != 80 && $_SERVER['HTTP_X_FORWARDED_PORT'] != 443) {
                    $url .= ':' . $_SERVER['HTTP_X_FORWARDED_PORT'];
                }
            } elseif (!empty($_SERVER['SERVER_PORT'])) {
                if ($_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443) {
                    $url .= ':' . $_SERVER['SERVER_PORT'];
                }
            }

            // Add subdirectory if defined
            if (defined('KNOWN_SUBDIRECTORY')) {
                $url .= '/' . KNOWN_SUBDIRECTORY;
            }

            return $url . '/';
        }

        /**
         * Build URL from domain environment variable
         */
        private function buildUrlFromDomain(string $domain): string
        {
            $url = (Page::isSSL() ? 'https://' : 'http://') . $domain;

            $port = getenv('KNOWN_PORT') ?: 80;
            if ($port != 80 && $port != 443) {
                $url .= ':' . $port;
            }

            if (defined('KNOWN_SUBDIRECTORY')) {
                $url .= '/' . KNOWN_SUBDIRECTORY;
            }

            return $url . '/';
        }

        /**
         * Get display URL (with correct protocol)
         */
        public function getDisplayURL(): string
        {
            $url = $this->getURL();
            $scheme = parse_url($url, PHP_URL_SCHEME);
            
            $newScheme = Page::isSSL() ? 'https:' : 'http:';
            $url = str_replace($scheme . ':', $newScheme, $url);
            
            if (substr($url, 0, 1) == ':') {
                $url = substr($url, 1);
            }

            return $url;
        }

        /**
         * Get the canonical URL
         */
        public function getURL(): string
        {
            return !empty($this->config['url']) ? $this->config['url'] : '/';
        }

        /**
         * Get static assets URL
         */
        public function getStaticURL(): string
        {
            return !empty($this->config['static_url']) ? $this->config['static_url'] : $this->getDisplayURL();
        }

        /**
         * Get URL without scheme or trailing slash
         */
        public function getSchemelessURL(bool $precedingSlashes = false): string
        {
            $url = $this->getURL();
            $scheme = parse_url($url, PHP_URL_SCHEME);
            
            if ($precedingSlashes) {
                $url = str_replace($scheme . ':', '', $url);
            } else {
                $url = str_replace($scheme . '://', '', $url);
            }
            
            return rtrim($url, '/');
        }

        /**
         * Sanitize a URL
         */
        public function sanitizeURL(?string $url): string|false
        {
            if (empty($url)) {
                return false;
            }

            $urlPieces = parse_url($url);
            if (!$urlPieces) {
                return false;
            }

            if (substr($url, -1, 1) != '/') {
                $url .= '/';
            }

            return $url;
        }

        /**
         * Sanitize attachment URL with base host override
         */
        public function sanitizeAttachmentURL(string $url): string
        {
            if (!empty($this->config['attachment_base_host'])) {
                $host = parse_url($url, PHP_URL_HOST);
                return str_replace($host, $this->config['attachment_base_host'], $url);
            }

            return $url;
        }

        /**
         * Get host for file paths
         */
        public function getFileBaseDirName(): string
        {
            $host = $this->pathHost();
            
            if (!empty($this->config['file_path_host'])) {
                $host = $this->config['file_path_host'];
            }

            // Allow event-based override
            if (class_exists('\Idno\Core\Idno') && \Idno\Core\Idno::site()) {
                $host = \Idno\Core\Idno::site()->events()->triggerEvent('file/path/host', ['host' => $host], $host);
            }

            return $host;
        }

        /**
         * Get normalized host for file paths
         */
        public function pathHost(): string
        {
            $host = parse_url($this->getURL(), PHP_URL_HOST) ?: 'localhost';
            return str_replace('www.', '', $host);
        }

        /**
         * Check if site has SSL
         */
        public function hasSSL(): bool
        {
            return substr_count($this->getURL(), 'https://') > 0 || !empty($this->config['force_ssl']);
        }
    }
}