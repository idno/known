<?php

    namespace Idno\Files {

        class MongoDBFile extends File
        {

            /**
             * Get this file's contents. For larger files this might not be wise.
             * @return mixed|string
             */
            function getBytes()
            {
         
            }
            
            function getSize() {
         
            }

            /**
             * Output the contents of the file to the buffer
             * @return mixed|void
             */
            function passThroughBytes()
            {
         
            }

            /**
             * Retrieves a stream resource referencing the file
             * @return mixed|resource
             */
            function getResource()
            {
         
            }

            /**
             * Delete this file
             */
            function delete()
            {
            }

            /**
             * Writes this file to the filename specified in $path
             * @param string $path
             * @return bool|mixed
             */
            function write($path)
            {
            }

            /**
             * Returns this file's filename
             * @return string
             */
            function getFilename()
            {
         
            }

        }

    }