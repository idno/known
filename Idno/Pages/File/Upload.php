<?php

    /**
     * File upload callback
     */

    namespace Idno\Pages\File {

        class Upload extends \Idno\Common\Page
        {

            function getContent()
            {
                // Not accessible via GET
            }

            function post()
            {
                if (\Idno\Core\Idno::site()->session()->isLoggedOn()) {
                    if (!empty($_FILES['file']['tmp_name'])) {
                        if (!\Idno\Core\Idno::site()->triggerEvent("file/upload", [], true)) {
                            exit;
                        }
                        if ($file = \Idno\Entities\File::createFromFile($_FILES['file']['tmp_name'], $_FILES['file']['name'], $_FILES['file']['type'], true, true)) {
                            echo json_encode(\Idno\Core\Idno::site()->config()->url . 'file/' . $file->file['_id']);
                        }
                    }
                }
            }

        }

    }