<?php

/**
 * Generic, backup viewer for entities
 */

namespace Idno\Pages\File {

    /**
     * Default class to serve the homepage
     */
    class View extends \Idno\Common\Page {

        // Handle GET requests to the entity

        function getContent() {
            // Check modified ts
            if ($cache = \Idno\Core\Idno::site()->cache()) {
                if ($modifiedts = $cache->load("{$this->arguments[0]}_modified_ts")) {
                    $this->lastModifiedGatekeeper($modifiedts); // Set 304 and exit if we've not modified this object
                }
            }

            if (!empty($this->arguments[0])) {
                $object = \Idno\Entities\File::getByID($this->arguments[0]);
            }

            if (empty($object))
                $this->noContent();


            session_write_close();  // Close the session early
            //header("Pragma: public");
            // Determine uploaded timestamp
            if ($object instanceof \MongoGridFSFile) {
                $upload_ts = $object->file['uploadDate']->sec;
            } else if (!empty($object->updated)) {
                $upload_ts = $object->updated;
            } else if (!empty($object->created)) {
                $upload_ts = $object->created;
            } else {
                $upload_ts = time();
            }

            header("Pragma: public");
            header("Cache-Control: public");
            header('Expires: ' . date(\DateTime::RFC1123, time() + (86400 * 30))); // Cache files for 30 days!
            $this->setLastModifiedHeader($upload_ts);
            if ($cache = \Idno\Core\Idno::site()->cache()) {
                $cache->store("{$this->arguments[0]}_modified_ts", $upload_ts);
            }
            if (!empty($object->file['mime_type'])) {
                header('Content-type: ' . $object->file['mime_type']);
            } else {
                header('Content-type: application/data');
            }

            header('Accept-Ranges: bytes');

            // Partial content
            if (isset($_SERVER['HTTP_RANGE'])) {

                $size = $object->getSize();
                $start = 0;
                $end = $size - 1;

                $c_start = $start;
                $c_end = $end;

                // Parse range
                list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);

                if ($range[0] == '-') {
                    // Range form "-123"
                    $c_start = (int)($size - (int)substr($range, 1));
                } else {
                    // Range form "123-" or "123-345"
                    $range = explode('-', $range);
                    $c_start = (int)$range[0];
                    
                    if (isset($range[1]) && is_numeric($range[1])) {
                        $c_end = (int)$range[1];
                    }
                }
                
                \Idno\Core\Idno::site()->logging()->debug("Partial content request for $c_start - $c_end bytes from $size available bytes");
                
                // Validate range
                if (
                        ($c_start > $c_end) || // Start after end
                        ($c_end > $size) || // End after size
                        ($c_start < 0) // Start less than zero
                ) {
                    $this->setResponse(416);
                    header('HTTP/1.1 416 Requested Range Not Satisfiable');
                    exit;
                }
                
                // Now output headers and partial content
                $this->setResponse(206);
                header('Content-Length: ' . ($c_end-$c_start));
                header("Content-Range: bytes $c_start-$c_end/$size");
                
                if ($stream = $object->getResource()) {
                    fseek($stream, $c_start);
                    echo fread($stream, $c_end-$c_start);
                }
                
            } else {
                
                header('Content-Length: ' . $object->getSize());
            
                if (is_callable(array($object, 'passThroughBytes'))) {
                    $object->passThroughBytes();
                } else {
                    if ($stream = $object->getResource()) {
                        while (!feof($stream)) {
                            echo fread($stream, 8192);
                        }
                    }
                }
            }
        }

    }

}