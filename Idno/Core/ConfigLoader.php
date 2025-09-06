<?php

/**
 * Configuration Loader
 * 
 * Extracts configuration loading logic from Config.php for better separation of concerns
 *
 * @package    idno
 * @subpackage core
 */

namespace Idno\Core {

    class ConfigLoader
    {
        private $path;
        private $host;

        public function __construct(string $path, string $host)
        {
            $this->path = $path;
            $this->host = $host;
        }

        /**
         * Load configuration from various sources
         */
        public function loadConfiguration(): array
        {
            $config = [];

            // Load from database
            $dbConfig = $this->loadFromDatabase();
            if ($dbConfig) {
                $config = array_replace_recursive($config, $dbConfig);
            }

            // Load from files
            $fileConfig = $this->loadFromFiles();
            if ($fileConfig) {
                $config = array_replace_recursive($config, $fileConfig);
            }

            return $config;
        }

        /**
         * Load configuration from database
         */
        private function loadFromDatabase(): ?array
        {
            $config = \Idno\Core\Idno::site()->db()->getAnyRecord('config');
            if (!$config) {
                return null;
            }

            // Remove sensitive data that shouldn't be loaded from database
            $sensitiveKeys = [
                'dbname', 'dbpass', 'dbhost', 'dbstring', 'path', 'url', 'host',
                'feed', 'uploadpath', 'antiplugins', 'alwaysplugins', 'session_path',
                'session_hash_function', 'sessions_database', 'sessions_storage',
                'cookie_jar', 'proxy_string', 'proxy_type', 'disable_ssl_verify',
                'upload_tmp_dir', 'bypass_fulltext_search'
            ];

            foreach ($sensitiveKeys as $key) {
                unset($config[$key]);
            }

            return $config;
        }

        /**
         * Load configuration from ini/json files
         */
        private function loadFromFiles(): array
        {
            $config = [];

            $searchPaths = [
                $this->path,                    // Old location (deprecated)
                $this->path . '/configuration'  // New location
            ];

            foreach ($searchPaths as $path) {
                // Load INI files
                $iniConfig = $this->loadIniFile($path . '/config.ini');
                if ($iniConfig) {
                    $config = array_replace_recursive($config, $iniConfig);
                }

                // Load JSON files
                $jsonConfig = $this->loadJsonFile($path . '/config.json');
                if ($jsonConfig) {
                    $config = array_replace_recursive($config, $jsonConfig);
                }

                // Load per-domain configuration
                $domainConfig = $this->loadIniFile($path . '/' . $this->host . '.ini');
                if ($domainConfig) {
                    // Don't merge plugin settings
                    unset($config['initial_plugins'], $config['alwaysplugins'], $config['antiplugins']);
                    $config = array_replace_recursive($config, $domainConfig);
                }
            }

            // Load environment variables
            $envConfig = $this->loadFromEnvironment();
            if ($envConfig) {
                $config = array_replace_recursive($config, $envConfig);
            }

            return $config;
        }

        /**
         * Load configuration from INI file
         */
        private function loadIniFile(string $filepath): ?array
        {
            if (!file_exists($filepath)) {
                return null;
            }

            $config = @parse_ini_file($filepath, true);
            return $config ?: null;
        }

        /**
         * Load configuration from JSON file
         */
        private function loadJsonFile(string $filepath): ?array
        {
            if (!file_exists($filepath)) {
                return null;
            }

            $json = file_get_contents($filepath);
            if (!$json) {
                return null;
            }

            $config = json_decode($json, true);
            return $config ?: null;
        }

        /**
         * Load configuration from environment variables
         */
        private function loadFromEnvironment(): array
        {
            $config = [];

            // Handle database URL
            if (array_key_exists('KNOWN_DATABASE_URL', $_ENV)) {
                $parsed = parse_url($_ENV['KNOWN_DATABASE_URL']);
                $config['database'] = $parsed['scheme'];
                $config['dbname'] = basename($parsed['path']);
                $config['dbuser'] = $parsed['user'];
                $config['dbpass'] = $parsed['pass'];
                $config['dbhost'] = $parsed['host'];
                $config['dbport'] = $parsed['port'];
            }

            // Handle cloud storage
            $config = array_merge($config, $this->loadCloudStorageConfig());

            // Handle generic KNOWN_ environment variables
            foreach ($_SERVER as $name => $val) {
                if (substr($name, 0, 6) == 'KNOWN_') {
                    $configKey = strtolower(str_replace('KNOWN_', '', $name));
                    $config[$configKey] = $val;
                }
            }

            return $config;
        }

        /**
         * Load cloud storage configuration from environment
         */
        private function loadCloudStorageConfig(): array
        {
            $config = [];

            $cloudcube = array_key_exists('CLOUDCUBE_URL', $_ENV);
            $aws_s3 = array_key_exists('KNOWN_AWS_S3_BUCKET', $_ENV);

            if ($cloudcube) {
                $parsed = parse_url($_ENV['CLOUDCUBE_URL']);
                $host_parts = explode('.', $parsed['host']);
                $bucket = $host_parts[0];
                
                $config['aws_key'] = $_ENV['CLOUDCUBE_ACCESS_KEY_ID'];
                $config['aws_secret'] = $_ENV['CLOUDCUBE_SECRET_ACCESS_KEY'];
                $config['aws_bucket'] = $bucket;
                $config['aws_region'] = $bucket == 'cloud-cube-eu' ? 'eu-west-1' : 'us-east-1';
                $config['filesystem'] = 'local';
                $config['uploadpath'] = "s3://{$bucket}{$parsed['path']}";
            } elseif ($aws_s3) {
                $bucket = $_ENV['KNOWN_AWS_S3_BUCKET'];
                $path = str_replace('//', '/', '/' . $_ENV['KNOWN_AWS_S3_PATH_PREFIX']);
                
                $config['aws_key'] = $_ENV['KNOWN_AWS_S3_ACCESS_KEY_ID'];
                $config['aws_secret'] = $_ENV['KNOWN_AWS_S3_SECRET_ACCESS_KEY'];
                $config['aws_bucket'] = $bucket;
                $config['aws_region'] = $_ENV['KNOWN_AWS_S3_REGION'];
                $config['filesystem'] = 'local';
                $config['uploadpath'] = "s3://{$bucket}{$path}";
            }

            // Override with specific environment variables
            if (array_key_exists('KNOWN_AWS_S3_REGION', $_ENV)) {
                $config['aws_region'] = $_ENV['KNOWN_AWS_S3_REGION'];
            }
            if (array_key_exists('KNOWN_UPLOAD_PATH', $_ENV)) {
                $config['uploadpath'] = $_ENV['KNOWN_UPLOAD_PATH'];
            }

            return $config;
        }

        /**
         * Sanitize configuration values
         */
        public function sanitizeConfig(array $config): array
        {
            // Sanitize upload path
            if (isset($config['uploadpath'])) {
                $config['uploadpath'] = rtrim($config['uploadpath'], ' /') . '/';
            }

            // Remove path and host as they should always be derived
            unset($config['path'], $config['host']);

            return $config;
        }
    }
}