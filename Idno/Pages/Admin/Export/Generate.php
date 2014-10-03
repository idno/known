<?php

    namespace Idno\Pages\Admin\Export {

        use Idno\Common\Page;
        use Idno\Core\Migration;
        use Idno\Entities\File;

        class Generate extends Page {

            function getContent() {
                $this->forward(\Idno\Core\site()->config()->getURL() . 'admin/export/');
            }

            function postContent() {

                $this->adminGatekeeper();

                // Flag that a site export has been requested
                \Idno\Core\site()->config->export_last_requested = time();
                \Idno\Core\site()->config->export_in_progress = 1;
                \Idno\Core\site()->config->save();

                $this->forward($_SERVER['HTTP_REFERER'], false);

                function shutdown() {
                    posix_kill(posix_getpid(), SIGHUP);
                }

                if ($pid = \pcntl_fork()) {
                    return;
                }

                ob_end_clean();

                fclose(STDIN);  // Close all of the standard
                fclose(STDOUT); // file descriptors as we
                fclose(STDERR); // are running as a daemon.

                register_shutdown_function('shutdown');

                if (posix_setsid() < 0)
                    return;

                if ($pid = pcntl_fork()) {
                    return;
                }

                sleep(10);

                // Eliminate time limit - this could take a while
                set_time_limit(0);

                if ($path = Migration::createCompressedArchive()) {

                    $filename = \Idno\Core\site()->config()->host . '.tar.gz';
                    /*                    header('Content-disposition: attachment;filename=' . $filename);
                                        if ($fp = fopen($path, 'r')) {
                                            while ($content = fread($fp, 4096)) {
                                                echo $content;
                                            }
                                        }
                                        fclose($fp);*/

                    if ($file = File::createFromFile($path, $filename)) {
                        \Idno\Core\site()->config->export_filename = $filename;
                        \Idno\Core\site()->config->export_file_id = $file;
                        \Idno\Core\site()->config->export_in_progress = 0;
                        \Idno\Core\site()->config->save();
                    }

                    exit;

                }

            }

        }

    }