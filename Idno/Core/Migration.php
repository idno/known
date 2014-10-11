<?php

    namespace Idno\Core {

        use Idno\Entities\File;

        class Migration extends \Idno\Common\Component {

            /**
             * Prepares an archive containing all of this site's data.
             * @return string
             */
            static function exportToFolder($dir = false) {

                set_time_limit(0);  // Switch off the time limit for PHP
                site()->currentPage()->setPermalink(true);

                // Prepare a unique name for the archive
                $name = md5(time() . rand(0,9999) . site()->config()->getURL());

                // If $folder is false or doesn't exist, use the temporary directory and ensure it has a slash on the end of it
                if (!is_dir($dir)) {
                    $dir = sys_get_temp_dir();
                }
                if (substr($dir, -1) != DIRECTORY_SEPARATOR) {
                    $dir .= DIRECTORY_SEPARATOR;
                }

                // Make the temporary directory, or fail out
                if (!@mkdir($dir . $name)) {
                    return false;
                }
                $json_path = $dir . $name . DIRECTORY_SEPARATOR . 'json' . DIRECTORY_SEPARATOR;
                if (!@mkdir($json_path)) {
                    return false;
                }
                $html_path = $dir . $name . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR;
                if (!@mkdir($html_path)) {
                    return false;
                }
                $file_path = $dir . $name . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR;
                if (!@mkdir($file_path)) {
                    return false;
                }

                // If we've made it here, we've created a temporary directory with the hash name

                $config = array(
                    'url'   => site()->config()->getURL(),
                    'title' => site()->config()->getTitle()
                );

                file_put_contents($dir . $name . DIRECTORY_SEPARATOR . 'known.json', json_encode($config));

                // Let's export everything.
                $fields = array();
                $query_parameters = array('entity_subtype' => array('$not' => array('$in' => array('Idno\Entities\ActivityStreamPost'))));
                $collection = 'entities';
                if ($results = site()->db()->getRecords($fields, $query_parameters, 99999, 0, $collection)) {
                    foreach ($results as $id => $row) {
                        $object = site()->db()->rowToEntity($row);
                        if (!empty($object->_id)) {
                            $object_name = $object->_id;
                            if ($attachments = $object->attachments) {
                                foreach($attachments as $key => $attachment) {
                                    if ($data = File::getFileDataFromAttachment($attachment)) {
                                        $filename = $attachment['_id'];
                                        if ($ext = pathinfo($attachment['url'], PATHINFO_EXTENSION)) {
                                            $filename .= '.' . $ext;
                                        }
                                        file_put_contents($file_path . $filename, $data);
                                        $attachments[$key]['url'] = '../files/' . $filename;
                                    }
                                }
                                $object->attachments = $attachments;
                            }
                            $activityStreamPost = new \Idno\Entities\ActivityStreamPost();
                            $owner              = $object->getOwner();
                            $activityStreamPost->setOwner($owner);
                            $activityStreamPost->setActor($owner);
                            $activityStreamPost->setTitle(sprintf($object->getTitle(), $owner->getTitle(), $object->getTitle()));
                            $activityStreamPost->setVerb('post');
                            $activityStreamPost->setObject($object);
                            file_put_contents($json_path . $object_name . '.json', json_encode($object));
                            if (is_callable(array($object, 'draw'))) {
                                file_put_contents($html_path . $object_name . '.html', $activityStreamPost->draw());
                            }
                            //unset($results[$id]);
                            //unset($object);
                            gc_collect_cycles();    // Clean memory
                        }
                    }
                }

                // As we're successful, return the unique name of the archive
                return $dir . $name;

            }

            /**
             * Given the path to a Known export, creates a complete .tar.gz file and returns the path to that.
             * If $save_path is false, will save to the temporary folder.
             *
             * @param $path
             * @param $save_path
             * @return string
             */
            static function archiveExportFolder($path, $save_path = false) {

                if (!is_dir($path)) {
                    return false;
                }
                if (substr($path, -1) != DIRECTORY_SEPARATOR) {
                    $path .= DIRECTORY_SEPARATOR;
                }
                if (!file_exists($path . 'known.json')) {
                    return false;
                }
                if (!class_exists('PharData')) {
                    return false;
                }

                $filename = str_replace('.','_',site()->config()->host);

                if (file_exists('/var/tmp/' . $filename . '.tar')) {
                    @unlink('/var/tmp/' . $filename . '.tar');
                    @unlink('/var/tmp/' . $filename . '.tar.gz');
                }

                $archive = new \PharData('/var/tmp/' . $filename . '.zip');
                $archive->buildFromDirectory($path);
                //$archive->compress(\Phar::GZ);

                return $archive->getPath();

            }

            /**
             * Wrapper function that exports Known data and returns the path to the archive of it.
             * @return bool|string
             */
            static function createCompressedArchive() {
                if ($path = self::exportToFolder()) {
                    if ($archive = self::archiveExportFolder($path)) {
                        self::cleanUpFolder($path);
                        return $archive;
                    }
                }
                return false;
            }

            /**
             * Given the path to an archive folder, recursively removes it
             * @param $path
             */
            static function cleanUpFolder($path) {
                foreach(glob("{path}/*") as $file)
                {
                    if(is_dir($file)) {
                        self::cleanUpFolder($file);
                    } else {
                        @unlink($file);
                    }
                }
                @rmdir($path);
            }

        }

    }