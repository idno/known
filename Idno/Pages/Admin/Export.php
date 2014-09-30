<?php

    namespace Idno\Pages\Admin {

        use Idno\Common\Page;
        use Idno\Core\Migration;

        class Export extends Page {

            function getContent() {

                $this->adminGatekeeper();

                // Eliminate time limit - this could take a while
                set_time_limit(0);

                if ($path = Migration::createCompressedArchive()) {

                    \Idno\Core\site()->session()->addMessage("ARchive: {$path}");

                    $filename = \Idno\Core\site()->config()->host . '.tar.gz';
                    header('Content-disposition: attachment;filename=' . $filename);
                    if ($fp = fopen($path, 'r')) {
                        while ($content = fread($fp, 4096)) {
                            echo $content;
                        }
                    }
                    fclose($fp);
                    exit;

                } else {
                    \Idno\Core\site()->session()->addMessage("We couldn't generate an archive of your data.");
                    $this->forward($_SERVER['HTTP_REFERER']);
                }

            }

            function postContent() {
                $this->getContent();
            }

        }

    }