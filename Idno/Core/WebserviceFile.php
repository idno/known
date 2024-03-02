<?php

/**
 * Utility wrapper around files that will be used in web service calls
 *
 * @package    idno
 * @subpackage core
 */

namespace Idno\Core {

    class WebserviceFile
    {

        private $file;
        private $name;
        private $mime;

        function __construct($file, $mime, $name)
        {
            $this->file = $file;
            $this->mime = $mime;
            $this->name = $name;
        }

        /**
         * Return curl parameters supported by your system.
         *
         * @return \CURLFile|string
         */
        function getCurlParameters()
        {

            if (class_exists('CURLFile')) {
                return new \CURLFile($this->file, $this->mime, $this->name);
            } else {
                return "@{$this->file};filename={$this->name};type={$this->mime}";
            }
        }

        /**
         * Converts an "@" formatted file string into a WebserviceFile
         *
         * @param  string $fileuploadstring
         * @return WebserviceFile|false
         */
        static function createFromCurlString($fileuploadstring)
        {
            if ($fileuploadstring[0] == '@') {
                $bits = explode(';', $fileuploadstring);

                $file = $name = $mime = null;

                foreach ($bits as $bit) {
                    // File
                    if ($bit[0] == '@') {
                        $file = trim($bit, '@ ;');
                    }
                    if (strpos($bit, 'filename') !== false) {
                        $tmp = explode('=', $bit);
                        $name = trim($tmp[1], ' ;');
                    }
                    if (strpos($bit, 'type') !== false) {
                        $tmp = explode('=', $bit);
                        $mime = trim($tmp[1], ' ;');
                    }
                }

                if ($file) {

                    if (file_exists($file)) {
                        return new WebserviceFile($file, $mime, $name);
                    }
                }
            }

            return false;
        }

    }

}
