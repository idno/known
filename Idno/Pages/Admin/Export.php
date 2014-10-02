<?php

    namespace Idno\Pages\Admin {

        use Idno\Common\Page;
        use Idno\Core\Migration;

        class Export extends Page {

            function getContent() {

                $this->adminGatekeeper();

                function shutdown() {
                    posix_kill(posix_getpid(), SIGHUP);
                }

                if ($pid = pcntl_fork()) {
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
                    header('Content-disposition: attachment;filename=' . $filename);
                    if ($fp = fopen($path, 'r')) {
                        while ($content = fread($fp, 4096)) {
                            echo $content;
                        }
                    }
                    fclose($fp);

                    // TODO: notify the user that the archive is ready;

                    exit;

                }

            }

            function postContent() {
                $this->getContent();
            }

        }

    }