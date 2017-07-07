<?php

namespace Idno\Caching {

    /**
     * Implement a persistent cache using the local filesystem.
     * 
     * Uses cachepath, then uploadpath and finally system temp as the base path.
     */
    class FilesystemCache extends PersistentCache {
        
        private static $path;
        
        public function __construct() {
            
            $domain = md5(\Idno\Core\Idno::site()->config()->host);
            if (empty($domain))
                throw new \RuntimeException("No domain specified for cache");
            
            $pathbase = \Idno\Core\Idno::site()->config()->cachepath;
            if (empty($pathbase))
                $pathbase = \Idno\Core\Idno::site()->config()->uploadpath;
            if (empty($pathbase))
                $pathbase = \Idno\Core\Idno::site()->config()->getTempDir();
            
            
            self::$path = '/' . trim($pathbase, ' /') . '/' . $domain . '/';
        }
        
        public function delete($key) {
            unlink(self::$path . '/' . sha1($key));
        }

        public function load($key) {
            if (file_exists(self::$path .  sha1($key)))
                return file_get_contents(self::$path .  sha1($key));
        }

        public function size() {
            
            // Folder size code from :https://gist.github.com/eusonlito/5099936
            $size = 0;
            foreach (glob(rtrim(self::$path, '/').'/*', GLOB_NOSORT) as $each) {
                $size += is_file($each) ? filesize($each) : folderSize($each);
            }
            return $size;
        }

        public function store($key, $value) {
            @mkdir(self::$path, 0700);
            file_put_contents(self::$path . sha1($key), $value);
            
        }

    }

}