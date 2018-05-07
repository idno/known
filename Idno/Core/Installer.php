<?php

namespace Idno\Core {

    /**
     * Installation tools for installing Known and validating requirements.
     */
    abstract class Installer {
        
        protected $root_path;
        
        public function __construct() {
            $this->root_path = dirname(dirname(dirname(__FILE__)));
        }

        /**
         * Is known installed (warmup complete - configuration correct)
         */
        public function isInstalled() {

            foreach ([
                $this->root_path . '/config.ini',
                $this->root_path . '/configuration/config.ini'
            ] as $location) {
                if (file_exists($location)) {
                    if ($config = @parse_ini_file($location)) {
                        if (!empty($config)) {
                            return true;
                        }
                    }
                }
            }

            return false;
        }

        /**
         * Run installation
         */
        abstract public function run();

        /**
         * Check that the upload directory exists and is readable and writable
         * @param type $upload_path
         * @return boolean
         * @throws \RuntimeException
         */
        protected function checkUploadDirectory($upload_path) {
            if (file_exists($upload_path) && is_dir($upload_path)) {
                if (!is_readable($upload_path)) {
                    throw new \RuntimeException('We can\'t read data from ' . htmlspecialchars($upload_path) . ' - please check permissions and try again.');
                }
                if (!is_writable($upload_path)) {
                    throw new \RuntimeException('We can\'t write data to ' . htmlspecialchars($upload_path) . ' - please check permissions and try again.');
                }
            } else {
                throw new \RuntimeException('The upload path ' . htmlspecialchars($upload_path) . ' either doesn\'t exist or isn\'t a directory.');
            }
            
            return true;
        }
        
        /**
         * Write the htaccess.
         * @todo Is this even necessary? Logic seems weird to me...
         */
        protected function writeHTAccess() {
            if (file_exists($this->root_path . '/.htaccess')) {
                if ($fp = @fopen($this->root_path . '/.htaccess', 'a')) {
                    fwrite($fp, "\n\n\n" . file_get_contents($this->root_path . '/warmup/webserver-configs/htaccess.dist'));
                }
            } else {
                @rename($this->root_path . '/warmup/webserver-configs/htaccess.dist', $this->root_path . '/.htaccess');
            }
        }
        
        /**
         * Write an apache config
         * 
         * @todo Necessary?
         * @todo Handle nginx?
         */
        protected function writeApacheConfig() {
            if (file_exists($this->root_path . '/.htaccess')) {
                if ($fp = @fopen($this->root_path . '/.htaccess', 'a')) {
                    fwrite($fp, "\n\n\n" . file_get_contents($this->root_path . '/warmup/webserver-configs/htaccess.dist'));
                }
            } else {
                @rename($this->root_path . '/warmup/webserver-configs/htaccess.dist', $this->root_path . '/.htaccess');
            }
        }
        
        /**
         * Write out the configuration
         * @param type $ini_file
         * @param type $name
         * @throws \RuntimeException
         */
        protected function writeConfig($ini_file, $name = 'config.ini') {
            
            if ($fp = @fopen($this->root_path. '/configuration/' . $name, 'w')) {
                fwrite($fp, $ini_file);
                fclose($fp);
                
            } else {
                throw new \RuntimeException("Could not write config file");
            }
            
        }
        
        /**
         * Install the mysql DB schema
         * @param type $host
         * @param type $dbname
         * @param type $user
         * @param type $pass
         * @param type $schema
         * @return boolean
         * @throws \RuntimeException
         */
        protected function installSchema(
                $host,
                $dbname,
                $user,
                $pass,
                $schema = 'mysql'
        ) {
            $dbname = preg_replace("/[^a-zA-Z0-9\_\.]/", "", $dbname); // Sanitise $dbname
            
            $database_string = $schema . ':';
            $database_string .= 'host=' . $host . ';';
            $database_string .= 'dbname=' . $dbname;

            $dbh = new \PDO($database_string, $user, $pass);
            if ($schema = @file_get_contents($this->root_path . '/warmup/schemas/mysql/mysql.sql')) {
                $dbh->exec('use `' . $dbname . '`');
                if (!$dbh->exec($schema)) {
                    $err = $dbh->errorInfo();
                    if ($err[0] === '00000') {
                        // exec() might return false (no rows affected) and still have been successful
                        // http://php.net/manual/en/pdo.exec.php#118156
                    } else if ($err[0] === '01000') {
                        error_log('INSTALLATION WARNING: Installed database schema with warnings: '.$err[2]);
                    } else {
                        throw new \RuntimeException('We couldn\'t automatically install the database schema: '.$err[2]);
                    }
                }
            } else {
                throw new \RuntimeException("We couldn't find the schema doc.");
            }
            
            return true;
        }

        /**
         * Return whether mod rewrite is available
         */
        public static function rewriteAvailable() {
            $modules = apache_get_modules();
            if (in_array('mod_rewrite', apache_get_modules())) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * Check the PHP version.
         * 
         * @return 'ok','fail','warn'
         */
        public static function checkPHPVersion() {
            if (version_compare(phpversion(), '7.0') >= 0) {
                return 'ok';
            } else if (version_compare(phpversion(), '5.6') >= 0) {
                return 'warn';
            } else {
                return 'fail';
            }
        }

        /**
         * Return the required modules
         */
        public static function requiredModules() {
            return [
                'curl',
                'date',
                'dom',
                'gd',
                'json',
                'libxml',
                'mbstring',
                'pdo',
                'pdo_mysql',
                'reflection',
                'session',
                'simplexml',
                'openssl',
                'gettext',
            ];
        }

    }

}