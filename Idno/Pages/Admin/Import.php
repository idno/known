<?php

    namespace Idno\Pages\Admin {

        use Idno\Common\Page;
        use Idno\Core\Migration;

        class Import extends Page
        {

            function getContent()
            {

                $this->adminGatekeeper();

                $t = \Idno\Core\Idno::site()->template();
                $t->__(array(
                    'title' => 'Import data',
                    'body'  => $t->draw('admin/import'),
                ))->drawPage();

            }

            function postContent()
            {

                $this->adminGatekeeper();

                define('KNOWN_NOMENTION', true);

                $import_type = $this->getInput('import_type');

                if (empty($_FILES['import'])) {
                    \Idno\Core\Idno::site()->session()->addMessage("You need to upload an import file to continue.");
                } else if (!($xml = @file_get_contents($_FILES['import']['tmp_name']))) {
                    \Idno\Core\Idno::site()->session()->addMessage("We couldn't open the file you uploaded. Please try again.");
                } else {
                    \Idno\Core\Idno::site()->session()->addMessage("Your {$import_type} import has started.");
                }

                session_write_close();
                $this->forward(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/import/', false);

                ignore_user_abort(true);    // This is dangerous, but we need export to continue

                header('Connection: close');
                header('Content-length: ' . (string)ob_get_length());

                @ob_end_flush();            // Return output to the browser
                @ob_end_clean();
                @flush();

                sleep(10);                  // Pause

                set_time_limit(0);          // Eliminate time limit - this could take a while

                $imported = false;
                switch (strtolower($import_type)) {

                    case 'blogger':
                        $imported = Migration::importBloggerXML($xml);
                        break;
                    case 'wordpress':
                        $imported = Migration::importWordPressXML($xml);
                        break;

                }
                if ($imported) {
                    $mail = new Email();
                    $mail->setHTMLBodyFromTemplate('admin/import');
                    $mail->setTextBodyFromTemplate('admin/import');
                    $mail->addTo(\Idno\Core\Idno::site()->session()->currentUser()->email);
                    $mail->setSubject("Your data import has completed");
                    $mail->send();
                }

            }

        }

    }