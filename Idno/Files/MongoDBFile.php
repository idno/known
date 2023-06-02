<?php

namespace Idno\Files {

    class MongoDBFile extends File
    {

        private $bucket;

        /**
         * Get this file's contents. For larger files this might not be wise.
         *
         * @return mixed|string
         */
        function getBytes()
        {
            $contents = '';
            $handle = $this->getResource();
            while (!feof($handle)) {
                $contents .= fread($handle, 8192);
            }
            fclose($handle);

            return $contents;
        }

        function getSize()
        {
            if ($f = $this->getResource()) {

                //                    fseek($f, -1, SEEK_END);
                //
                //                    $size = ftell($f);
                //
                //                    fclose($f);
                //
                //                    return $size;

                // There has to be a better way
                return strlen($this->getBytes());
            }

            return false;
        }

        /**
         * Output the contents of the file to the buffer
         *
         * @return mixed|void
         */
        function passThroughBytes()
        {
            if ($file_handle = $this->getResource()) {
                ob_end_flush();
                fpassthru($file_handle);
                fclose($file_handle);
            }
        }

        /**
         * Retrieves a stream resource referencing the file
         *
         * @return mixed|resource
         */
        function getResource()
        {
            $resource = $this->bucket->openDownloadStream(new \MongoDB\BSON\ObjectID($this->_id));

            return $resource;
        }

        /**
         * Delete this file
         */
        function delete()
        {
            try {
                return $this->bucket->delete(new \MongoDB\BSON\ObjectID($this->_id));
            } catch (\Exception $e) {
                \Idno\Core\site()->logging()->debug($e->getMessage());
            }
        }

        /**
         * Writes this file to the filename specified in $path
         *
         * @param  string $path
         * @return bool|mixed
         */
        function write($path)
        {
            try {
                if ($out = fopen($path, 'wb')) {
                    $this->bucket->downloadToStream(new \MongoDB\BSON\ObjectID($this->_id), $out);

                    fclose($out);

                    return true;
                }
            } catch (\Exception $e) {
                \Idno\Core\site()->logging()->debug($e->getMessage());
            }

            return false;
        }

        /**
         * Returns this file's filename
         *
         * @return string
         */
        function getFilename()
        {
            return $this->filename;
        }


        public function setBucket($bucket)
        {

            $this->bucket = $bucket;
        }

    }

}
