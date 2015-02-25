<?php

    namespace Idno\Pages\Admin {

        use Idno\Common\Page;
        use Idno\Core\Migration;
        use Idno\Entities\File;

        class Import extends Page {

            function getContent() {

                $this->adminGatekeeper();

                $t = \Idno\Core\site()->template();
                $t->__(array(
                    'title' => 'Import data',
                    'body' => $t->draw('admin/import'),
                ))->drawPage();

            }

            function postContent() {

                $this->adminGatekeeper();

                $import_type = $this->getInput('import_type');

                if (empty($_FILES['import'])) {
                    \Idno\Core\site()->session()->addMessage("You need to upload an import file to continue.");
                } else if (!($xml = @file_get_contents($_FILES['import']['tmp_name']))) {
                    \Idno\Core\site()->session()->addMessage("We couldn't open the file you uploaded. Please try again.");
                } else {
                    \Idno\Core\site()->session()->addMessage("Your {$import_type} import has started.");
                }

                $this->forward(\Idno\Core\site()->config()->getDisplayURL() . 'admin/import/', false);

                ignore_user_abort(true);    // This is dangerous, but we need export to continue

                session_write_close();

                header('Connection: close');
                header('Content-length: ' . (string) ob_get_length());

                @ob_end_flush();            // Return output to the browser
                @ob_end_clean();
                @flush();

                sleep(10);                  // Pause

                set_time_limit(0);          // Eliminate time limit - this could take a while

                if (strtolower($import_type) == 'blogger') {
                    if (Migration::ImportBloggerXML($xml)) {

                        $mail = new Email();
                        $mail->setHTMLBodyFromTemplate('admin/import');
                        $mail->addTo(\Idno\Core\site()->session()->currentUser()->email);
                        $mail->setSubject("Your data import has completed");
                        $mail->send();

                    }
                }

            }

        }

    }