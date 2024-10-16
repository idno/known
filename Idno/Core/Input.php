<?php

    /**
     * Input handling methods
     *
     * @package    idno
     * @subpackage core
     */

namespace Idno\Core {

    class Input extends \Idno\Common\Component
    {

        /**
         * Retrieves input from http request, and performs optional filtering.
         *
         * @param  string  $name    Name of the input variable
         * @param  mixed   $default A default return value if no value specified (default: null)
         * @param  boolean $filter  Whether or not to filter the variable for safety (default: null), you can pass
         *                          a callable method, function or enclosure with a definition like
         *                          function($name, $value), which will return the filtered result.
         * @return mixed
         */
        public static function getInput($name, $default = null, callable $filter = null)
        {
            if (!empty($name)) {

                $value = null;
                if (\Idno\Core\Idno::site()->request()->request->has($name)) {
                    try{
                    $value = \Idno\Core\Idno::site()->request()->request->get($name);
                    }catch(\Exception $e){
                        $value = \Idno\Core\Idno::site()->request()->request->all($name);
                    }                   
                }

                if (($value===null) && ($default!==null)) {
                    $value = $default;
                }
                if (!$value!==null) {
                    if (isset($filter) && is_callable($filter)) {
                        $value = call_user_func($filter, $name, $value);
                    }

                    // TODO, we may want to add some sort of system wide default filter for when $filter is null

                    return $value;
                }
            }

            return null;
        }

        /**
         * Retrieve single file from input.
         * Retrieve a formatted files array from input, if multiple files are found, this will be turned into
         * a sensible structure.
         *
         * @param string $name
         */
        public static function getFile($name)
        {
            $file = \Idno\Core\Idno::site()->request()->files->get($name);
            return ['name'=>$file->getClientOriginalName(),'type'=>$file->getClientMimeType(),'tmp_name'=>$file->getRealPath(),'error'=>$file->getError(),'size'=>$file->getSize()];
        }
        /**
         * Retrieve files from input.
         * Retrieve a formatted files array from input, if multiple files are found, this will be turned into
         * a sensible structure.
         *
         * @param string $name
         */
        public static function getFiles($name)
        {

            $files = \Idno\Core\Idno::site()->request()->files->all($name);
            // if (!is_array($files['name'])) {
            //     return $files; // Short circuit if there's only one entry for a name
            // }

            // Normalize file array,
            // HT: https://gist.github.com/umidjons/9893735
            $_files = [];
            // $_files_count = count($files['name']);
            // $_files_keys = array_keys($files);

            foreach ($files as $file) {
                array_push($_files,['name'=>$file->getClientOriginalName(),'type'=>$file->getClientMimeType(),'tmp_name'=>$file->getRealPath(),'error'=>$file->getError(),'size'=>$file->getSize()]);
        
            }

            return $_files;
        }

    }

}

