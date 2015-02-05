<?php

    /**
     * File upload callback
     */

    namespace Idno\Pages\File {

        use Idno\Entities\File;

        class Upload extends \Idno\Common\Page
        {

            function getContent()
            {
                // Not accessible via GET
            }

            function post()
            {
                if (\Idno\Core\site()->session()->isLoggedOn()) {
                    if (!empty($_FILES['file']['tmp_name'])) {
                        if (!\Idno\Core\site()->triggerEvent("file/upload",[],true)) {
                            exit;
                        }
                        if ($file = \Idno\Entities\File::createFromFile($_FILES['file']['tmp_name'], $_FILES['file']['name'], $_FILES['file']['type'], true, true)) {
                            echo json_encode(\Idno\Core\site()->config()->url . 'file/' . $file->file['_id']);
                            error_log(\Idno\Core\site()->config()->url . 'file/' . $file->file['_id']);
                        }
                    }
                }
            }

        }

    }