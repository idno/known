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
                $tmp_file = \Idno\Core\Input::getFile('file');
                if (!empty($tmp_file ['tmp_name'])) {
                    if (!\Idno\Core\Idno::site()->events()->triggerEvent("file/upload", [], true)) {
                        exit;
                    }
                    if ($file = \Idno\Entities\File::createFromFile($tmp_file ['tmp_name'], $tmp_file ['name'], $tmp_file ['type'], true, true)) {
                        \Idno\Core\Idno::site()->response()->setJsonContent( json_encode(\Idno\Core\Idno::site()->config()->url . 'file/' . $file->file['_id']));
                    }
                }
            }
        }

    }

}

